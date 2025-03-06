<?php

namespace App\Http\Controllers;

use App\Models\BatchStok;
use App\Models\DetailPenerimaanBarang;
use App\Models\PenerimaanBarang;
use App\Models\Produk;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class penerimaanBarangController extends Controller
{
    public function index()
    {
        $penerimaan = PenerimaanBarang::with('supplier', 'user')->latest()->get();
        $suppliers = Supplier::all();
        $produk = Produk::all();

        return view("admin.manajemenStok.penerimaanBarang", compact('penerimaan', 'suppliers', 'produk'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'supplier_id' => 'required|exists:supplier,id',
            'tgl_masuk' => 'required|date',
            'produk_id.*' => 'required|exists:produk,id',
            'jumlah.*' => 'required|integer|min:1',
        ]);

        // Mulai transaksi database
        DB::beginTransaction();
        try {
            // Membuat data penerimaan barang
            $penerimaan = PenerimaanBarang::create([
                'kode_penerimaan' => 'PB-' . time(),
                'tgl_masuk' => $request->tgl_masuk,
                'supplier_id' => $request->supplier_id,
                'total' => 0,
                'user_id' => Auth::id(),
            ]);

            $total = 0; // Inisialisasi total biaya penerimaan

            // Proses setiap produk yang diterima
            foreach ($request->produk_id as $index => $produk_id) {
                $produk = Produk::findOrFail($produk_id);
                $jumlah = $request->jumlah[$index];
                $harga_beli = $produk->harga_beli; // Harga beli diambil dari database

                // Hitung subtotal biaya penerimaan
                $sub_total = $harga_beli * $jumlah;


                // Simpan batch stok baru
                $batch = BatchStok::create([
                    'produk_id' => $produk_id,
                    'stok' => $jumlah,
                    'harga_beli' => $harga_beli,
                ]);

                // Simpan detail penerimaan barang
                DetailPenerimaanBarang::create([
                    'penerimaan_id' => $penerimaan->id,
                    'produk_id' => $produk_id,
                    'batch_id' => $batch->id,
                    'jumlah' => $jumlah,
                    'harga_beli' => $harga_beli,
                    'sub_total' => $sub_total,
                ]);

                //menambahkan total biaya penerimaan
                $total += $sub_total;
            }

            //mengupdate total biaya penerimaan
            $penerimaan->update(['total' => $total]);

            //menyimpan transaksi
            DB::commit();

            //mengembalikan pesan sukses
            return redirect()->back()->with('success', 'Penerimaan barang berhasil ditambahkan.');
        } catch (\Exception $e) {
            //membatalkan transaksi jika terjadi error
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $penerimaan = PenerimaanBarang::with('detailPenerimaan.produk', 'supplier', 'user')->findOrFail($id);
        return response()->json($penerimaan);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $penerimaan = PenerimaanBarang::findOrFail($id);

            // Menghapus detail penerimaan terkait
            $penerimaan->detailPenerimaan()->delete();

            // Menghapus penerimaan barang utama
            $penerimaan->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Penerimaan barang berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
