<?php

namespace App\Http\Controllers;

use App\Models\BatchStok;
use App\Models\Produk;
use Illuminate\Http\Request;

class BatchStokController extends Controller
{
    /**
     * Menampilkan daftar batch stok.
     *
     * Fungsi ini mengambil semua data batch stok beserta produk terkait dan menampilkannya
     * pada halaman admin untuk pengelolaan batch stok.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $batchStok = BatchStok::with('produk')->get();
        $produk = Produk::all();
        return view('admin.manajemenStok.batchStok', compact('batchStok', 'produk'));
    }

    /**
     * Menyimpan batch stok baru.
     *
     * Fungsi ini memvalidasi input yang diberikan oleh pengguna untuk produk, kode batch,
     * tanggal kedaluwarsa, stok gudang, dan stok toko, kemudian menyimpan data batch stok
     * baru ke dalam database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Memperbarui batch stok yang ada.
     *
     * Fungsi ini memvalidasi input yang diberikan untuk produk, kode batch, tanggal kedaluwarsa,
     * stok gudang, dan stok toko, kemudian memperbarui data batch stok yang sudah ada berdasarkan
     * ID batch stok yang diberikan.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Menghapus batch stok yang ada.
     *
     * Fungsi ini menghapus batch stok yang ada berdasarkan ID batch stok yang diberikan.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $batchStok = BatchStok::findOrFail($id);
        $batchStok->delete();

        return redirect()->route('batchstok.index')->with('success', 'Batch Stok berhasil dihapus.');
    }
}
