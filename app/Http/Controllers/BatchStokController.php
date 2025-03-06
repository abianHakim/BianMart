<?php

namespace App\Http\Controllers;

use App\Models\BatchStok;
use App\Models\Produk;
use Illuminate\Http\Request;

class BatchStokController extends Controller
{
    public function index()
    {
        $batchStok = BatchStok::with('produk')->get();
        $produk = Produk::all();
        return view('admin.manajemenStok.batchStok', compact('batchStok', 'produk'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'kode_batch' => 'required|string|max:50|unique:batch_stok,kode_batch',
            'expired_date' => 'nullable|date',
            'stok_gudang' => 'required|integer|min:0',
            'stok_toko' => 'required|integer|min:0',
        ]);

        BatchStok::create([
            'produk_id' => $request->produk_id,
            'kode_batch' => $request->kode_batch,
            'expired_date' => $request->expired_date,
            'stok_gudang' => $request->stok_gudang,
            'stok_toko' => $request->stok_toko,
        ]);

        return redirect()->route('batchstok.index')->with('success', 'Batch Stok berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $batchStok = BatchStok::findOrFail($id);

        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'kode_batch' => "required|string|max:50|unique:batch_stok,kode_batch,$id",
            'expired_date' => 'nullable|date',
            'stok_gudang' => 'required|integer|min:0',
            'stok_toko' => 'required|integer|min:0',
        ]);

        $batchStok->update([
            'produk_id' => $request->produk_id,
            'kode_batch' => $request->kode_batch,
            'expired_date' => $request->expired_date,
            'stok_gudang' => $request->stok_gudang,
            'stok_toko' => $request->stok_toko,
        ]);

        return redirect()->route('batchstok.index')->with('success', 'Batch Stok berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $batchStok = BatchStok::findOrFail($id);
        $batchStok->delete();

        return redirect()->route('batchstok.index')->with('success', 'Batch Stok berhasil dihapus.');
    }
}
