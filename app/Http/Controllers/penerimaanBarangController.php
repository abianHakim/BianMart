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
    public function index()
    {
        $penerimaan = PenerimaanBarang::with('supplier', 'user')->latest()->get();
        return view("admin.manajemenStok.penerimaanBarang", compact('penerimaan'));
    }


    public function create()
    {
        $suppliers = Supplier::all();
        $produk = Produk::all();
        return view("admin.manajemenStok.penerimaanBarangCreate", compact('suppliers', 'produk'));
    }
    public function getHargaBeli(Request $request)
    {
        $produk = Produk::find($request->produk_id);
        return response()->json([
            'harga_beli' => $produk ? $produk->harga_beli : 0
        ]);
    }


    public function getProdukBySupplier(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:supplier,id',
        ]);
        $produk = Produk::where('supplier_id', $request->supplier_id)
            ->get(['id', 'nama_barang']);
        return response()->json($produk);
    }

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

    public function invoice($id)
    {
        $penerimaan = PenerimaanBarang::with(['detailPenerimaan.produk', 'supplier', 'user'])->find($id);

        if (!$penerimaan) {
            return abort(404, 'Data tidak ditemukan!');
        }

        return view('admin.manajemenStok.penerimaanShow', compact('penerimaan'));
    }


    public function store(Request $request)
    {

        // dd($request->all());

        $request->validate([
            'supplier_id' => 'required|exists:supplier,id',
            'produk_id.*' => 'required|exists:produk,id',
            'jumlah.*' => 'required|integer|min:1',
            'harga_beli.*' => 'required|numeric|min:0',
            'sub_total.*' => 'nullable|numeric|min:0',
            'expired_date.*' => 'nullable|date|after_or_equal:today',
        ]);

        // dd($request->all());

        // Mulai transaksi database
        DB::beginTransaction();
        try {
            // Membuat data penerimaan barang
            $penerimaan = PenerimaanBarang::create([
                'kode_penerimaan' => 'PB-' . time(),
                'tgl_masuk' => now(),
                'supplier_id' => $request->supplier_id,
                'total' => 0,
                'user_id' => Auth::id(),
            ]);

            $total = 0; // Inisialisasi total biaya penerimaan

            // Proses setiap produk yang diterima
            foreach ($request->produk_id as $index => $produk_id) {
                $produk = Produk::findOrFail($produk_id);
                $jumlah = $request->jumlah[$index];
                $harga_beli_baru = $request->harga_beli[$index];
                $expired_date = $request->expired_date[$index] ?? null;

                // Hitung subtotal harga
                $sub_total = $harga_beli_baru * $jumlah;

                $tahunBulan = now()->format('y') . now()->format('m'); // Contoh: 2503

                // Cari batch terakhir di bulan ini untuk produk yang sama
                $lastBatch = BatchStok::where('produk_id', $produk_id)
                    ->where('kode_batch', 'like', "B-$tahunBulan-$produk_id-%")
                    ->latest('id')
                    ->first();

                // Tentukan nomor urut batch (mulai dari 01)
                $nextNumber = $lastBatch ? ((int)substr($lastBatch->kode_batch, -2)) + 1 : 1;
                // Buat kode batch yang lebih ringkas
                $kodeBatch = "B-$tahunBulan-$produk_id-" . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

                // Simpan batch stok baru
                $batch = BatchStok::create([
                    'produk_id' => $produk_id,
                    'kode_batch' => $kodeBatch,
                    'expired_date' => $expired_date,
                    'stok_gudang' => $jumlah,
                    'stok_toko' => 0,
                ]);


                // Simpan detail penerimaan barang
                DetailPenerimaanBarang::create([
                    'penerimaan_id' => $penerimaan->id,
                    'produk_id' => $produk_id,
                    'batch_id' => $batch->id,
                    'jumlah' => $jumlah,
                    'harga_beli' => $harga_beli_baru,
                    'sub_total' => $sub_total,
                ]);

                //menambahkan total biaya penerimaan
                $total += $sub_total;

                // update harga beli di produk
                if ($produk->harga_beli != $harga_beli_baru) {
                    $produk->update(['harga_beli' => $harga_beli_baru]);
                }

                $this->syncStokBarang($produk_id);
            }

            //mengupdate total biaya penerimaan
            $penerimaan->update(['total' => $total]);

            //menyimpan transaksi
            DB::commit();

            //mengembalikan pesan sukses
            return redirect()->route('penerimaan.index')->with('success', 'Penerimaan barang berhasil dibuat.');
        } catch (\Exception $e) {
            //membatalkan transaksi jika terjadi error
            DB::rollBack();
            return redirect()->back()->with('error', 'Kesalahan: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
        }
    }

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

            // Hapus detail penerimaan dan penerimaan utama
            $penerimaan->detailPenerimaan()->delete();
            $penerimaan->delete();

            return redirect()->back()->with('success', 'Penerimaan barang berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

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
