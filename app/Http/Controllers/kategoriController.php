<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use Illuminate\Http\Request;

class kategoriController extends Controller
{
    /**
     * Menampilkan daftar kategori produk.
     *
     * Fungsi ini mengambil semua data kategori produk dari database dan menampilkannya
     * pada halaman admin untuk pengelolaan kategori produk.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $kategori = KategoriProduk::all();

        return view('admin.manajemenProduk.kategori', compact('kategori'));
    }

    /**
     * Menyimpan kategori produk baru.
     *
     * Fungsi ini memvalidasi input yang diberikan oleh pengguna untuk nama kategori,
     * kemudian menyimpan data kategori produk baru ke dalam database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        KategoriProduk::create([
            'nama_kategori' => $request->nama_kategori,
        ]);
        
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Memperbarui kategori produk yang ada.
     *
     * Fungsi ini memvalidasi input yang diberikan untuk nama kategori dan memperbarui
     * kategori produk yang sudah ada berdasarkan ID kategori yang diberikan.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategori = KategoriProduk::findOrFail($id);
        $kategori->update([
            'nama_kategori' => $request->nama_kategori
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Menghapus kategori produk yang ada.
     *
     * Fungsi ini menghapus kategori produk yang ada berdasarkan ID kategori yang diberikan,
     * namun hanya jika kategori tersebut tidak memiliki produk terkait. Jika ada produk
     * yang terkait, penghapusan tidak diizinkan.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $kategori = KategoriProduk::findOrFail($id);

        if (\App\Models\Produk::where('kategori_id', $id)->exists()) {
            return redirect()->route('kategori.index')->with('error', 'Kategori tidak dapat dihapus karena memiliki produk terkait.');
        }
        $kategori->delete();

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
