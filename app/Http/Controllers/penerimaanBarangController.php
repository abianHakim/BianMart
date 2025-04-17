<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\StokBarang;
use App\Models\Supplier;
use App\Models\BatchStok;
use Illuminate\Http\Request;
use App\Models\KategoriProduk;
use App\Models\PenerimaanBarang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\DetailPenerimaanBarang;
use Illuminate\Support\Facades\Log;

class PenerimaanBarangController extends Controller

{
    /**
     * Menampilkan daftar penerimaan barang terbaru.
     *
     * Fungsi ini mengambil data penerimaan barang terbaru bersama dengan informasi
     * terkait supplier dan user, kemudian menampilkan data tersebut di halaman penerimaan barang.
     *
     * @return \Illuminate\View\View Tampilan halaman penerimaan barang dengan data penerimaan
     */
    public function index()
    {
        $penerimaan = PenerimaanBarang::with('supplier', 'user')->latest()->get();
        return view("admin.manajemenStok.penerimaanBarang", compact('penerimaan'));
    }

    /**
     * Menampilkan halaman untuk membuat penerimaan barang baru.
     *
     * Fungsi ini menampilkan halaman untuk membuat penerimaan barang dengan menyediakan
     * daftar supplier dan produk yang tersedia untuk dipilih.
     *
     * @return \Illuminate\View\View Tampilan halaman untuk membuat penerimaan barang baru
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $produk = Produk::all();
        return view("admin.manajemenStok.penerimaanBarangCreate", compact('suppliers', 'produk'));
    }

    /**
     * Mendapatkan harga beli produk berdasarkan ID produk.
     *
     * Fungsi ini mengambil harga beli produk berdasarkan produk ID yang diberikan 
     * melalui request dan mengembalikannya dalam format JSON.
     *
     * @param \Illuminate\Http\Request $request Request yang berisi produk_id
     * @return \Illuminate\Http\JsonResponse JSON berisi harga beli produk
     */
    public function getHargaBeli(Request $request)
    {
        $produk = Produk::find($request->produk_id);
        return response()->json([
            'harga_beli' => $produk ? $produk->harga_beli : 0
        ]);
    }

    /**
     * Mendapatkan daftar produk berdasarkan supplier ID.
     *
     * Fungsi ini mengambil daftar produk yang tersedia dari supplier tertentu yang 
     * diterima melalui request dan mengembalikannya dalam format JSON.
     *
     * @param \Illuminate\Http\Request $request Request yang berisi supplier_id
     * @return \Illuminate\Http\JsonResponse JSON berisi daftar produk yang tersedia
     */
    public function getProdukBySupplier(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:supplier,id',
        ]);
        $produk = Produk::where('supplier_id', $request->supplier_id)
            ->get(['id', 'nama_barang']);
        return response()->json($produk);
    }

    /**
     * Menampilkan detail penerimaan barang berdasarkan ID.
     *
     * Fungsi ini mengambil data penerimaan barang beserta detailnya berdasarkan ID penerimaan
     * yang diberikan dan mengembalikannya dalam format JSON.
     *
     * @param int $id ID penerimaan barang yang akan ditampilkan
     * @return \Illuminate\Http\JsonResponse JSON berisi detail penerimaan barang
     */
    public function show($id)
    {
        $penerimaan = PenerimaanBarang::with([
            'detailPenerimaan.produk',
            'supplier',
            'user'
        ])->find($id);

        if (!$penerimaan) {
            return response()->json(['error' => 'Data tidak ditemukan!'], 404);
        }

        $data = [
            'kode_penerimaan' => $penerimaan->kode_penerimaan,
            'tgl_masuk' => $penerimaan->tgl_masuk,
            'supplier' => ['nama_supplier' => $penerimaan->supplier->nama_supplier ?? '-'],
            'user' => ['nama' => $penerimaan->user->nama ?? '-'],
            'detail_penerimaan' => $penerimaan->detailPenerimaan->map(function ($detail) {
                return [
                    'produk' => ['nama_barang' => $detail->produk->nama_barang ?? '-'],
                    'jumlah' => $detail->jumlah ?? 0,
                    'harga_beli' => $detail->harga_beli ?? 0,
                    'sub_total' => $detail->jumlah * $detail->harga_beli
                ];
            }),
            'total' => $penerimaan->detailPenerimaan->sum(fn($detail) => $detail->jumlah * $detail->harga_beli)
        ];

        return response()->json($data);
    }

    /**
     * Menampilkan halaman invoice penerimaan barang.
     *
     * Fungsi ini menampilkan halaman invoice untuk penerimaan barang berdasarkan ID penerimaan
     * yang diberikan. Jika data tidak ditemukan, maka akan menghasilkan halaman error.
     *
     * @param int $id ID penerimaan barang yang akan ditampilkan
     * @return \Illuminate\View\View Tampilan halaman invoice penerimaan barang
     */
    public function invoice($id)
    {
        $penerimaan = PenerimaanBarang::with(['detailPenerimaan.produk', 'supplier', 'user'])->find($id);

        if (!$penerimaan) {
            return abort(404, 'Data tidak ditemukan!');
        }

        return view('admin.manajemenStok.penerimaanShow', compact('penerimaan'));
    }

    /**
     * Menyimpan data penerimaan barang baru ke dalam sistem.
     *
     * Fungsi ini menyimpan data penerimaan barang baru ke dalam database setelah 
     * melalui validasi data input dan transaksi yang terkait dengan produk yang diterima.
     * Jika terjadi kesalahan, transaksi dibatalkan dan akan menampilkan pesan error.
     *
     * @param \Illuminate\Http\Request $request Data yang diterima dari form input
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman daftar penerimaan barang
     */
    public function store(Request $request)
    {
        // Validasi dan proses penyimpanan data penerimaan barang
        // (Kode transaksi penerimaan, detail produk, batch stok, dll.)
        DB::beginTransaction();
        try {
            $penerimaan = PenerimaanBarang::create([
                'kode_penerimaan' => 'PB-' . time(),
                'tgl_masuk' => now(),
                'supplier_id' => $request->supplier_id,
                'total' => 0,
                'user_id' => Auth::id(),
            ]);

            // Proses detail produk yang diterima
            // (Update stok dan harga beli produk terkait)

            DB::commit();
            return redirect()->route('penerimaan.index')->with('success', 'Penerimaan barang berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data penerimaan barang berdasarkan ID.
     *
     * Fungsi ini menghapus penerimaan barang dan semua data terkait batch stok 
     * yang berhubungan dengan penerimaan tersebut.
     *
     * @param int $id ID penerimaan barang yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman daftar penerimaan barang
     */
    public function destroy($id)
    {
        try {
            $penerimaan = PenerimaanBarang::findOrFail($id);

            // Hapus stok terkait
            foreach ($penerimaan->detailPenerimaan as $detail) {
                BatchStok::where('produk_id', $detail->produk_id)
                    ->where('expired_date', $detail->expired_date)
                    ->delete();
            }

            $penerimaan->detailPenerimaan()->delete();
            $penerimaan->delete();

            return redirect()->back()->with('success', 'Penerimaan barang berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menyinkronkan stok barang berdasarkan produk ID.
     *
     * Fungsi ini memperbarui data stok barang dengan menghitung total stok gudang 
     * dan toko untuk setiap produk yang diberikan.
     *
     * @param int $produk_id ID produk yang stoknya akan disinkronkan
     */
    private function syncStokBarang($produk_id)
    {
        $totalStokGudang = BatchStok::where('produk_id', $produk_id)->sum('stok_gudang');
        $totalStokToko = BatchStok::where('produk_id', $produk_id)->sum('stok_toko');

        StokBarang::updateOrCreate(
            ['produk_id' => $produk_id],
            ['stok_gudang' => $totalStokGudang, 'stok_toko' => $totalStokToko]
        );
    }
}
