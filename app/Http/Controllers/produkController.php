<?php

namespace App\Http\Controllers;

use App\Imports\ProdukImport;
use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class produkController extends Controller
{
    /**
     * Menampilkan daftar produk dengan kategori dan supplier terkait.
     *
     * Fungsi ini mengambil semua produk beserta data kategori dan supplier terkait
     * untuk ditampilkan di halaman manajemen produk.
     *
     * @return \Illuminate\View\View Tampilan halaman produk dengan data produk, kategori, dan supplier
     */
    public function index()
    {
        $produk = Produk::with('kategori', 'supplier')->get();
        $kategori = KategoriProduk::all();
        $supplier = Supplier::all();
        return view('admin.manajemenProduk.produk', compact('produk', 'kategori', 'supplier'));
    }

    /**
     * Menyimpan data produk baru.
     *
     * Fungsi ini menyimpan data produk yang baru ditambahkan setelah melakukan validasi
     * terhadap input dari form. Gambar produk akan disimpan jika ada, dan produk baru
     * akan disimpan ke dalam database.
     *
     * @param \Illuminate\Http\Request $request Data yang diterima dari form input produk
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman sebelumnya dengan pesan sukses
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|unique:produk,kode_barang',
            'nama_barang' => 'required',
            'kategori_id' => 'required',
            'supplier_id' => 'nullable|exists:supplier,id',
            'harga_beli' => 'required|numeric',
            'persentase_keuntungan' => 'required|numeric|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('produk', $imageName, 'public');
        } else {
            $path = null;
        }

        Produk::create([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'kategori_id' => $request->kategori_id,
            'supplier_id' => $request->supplier_id,
            'harga_beli' => $request->harga_beli,
            'persentase_keuntungan' => $request->persentase_keuntungan,
            'deskripsi' => $request->deskripsi,
            'satuan' => 'pcs',
            'gambar' => $path,
        ]);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan!');
    }

    /**
     * Memperbarui data produk yang ada.
     *
     * Fungsi ini memperbarui data produk berdasarkan ID yang diberikan. Jika ada perubahan gambar,
     * gambar lama akan dihapus dan gambar baru akan disimpan. Setelah itu, produk yang diperbarui
     * akan disimpan kembali ke dalam database.
     *
     * @param \Illuminate\Http\Request $request Data yang diterima dari form input produk
     * @param int $id ID produk yang akan diperbarui
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman sebelumnya dengan pesan sukses
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_barang' => 'required',
            'nama_barang' => 'required',
            'kategori_id' => 'required',
            'supplier_id' => 'nullable|exists:supplier,id',
            'harga_beli' => 'required|numeric',
            'persentase_keuntungan' => 'required|numeric|min:0',
            'deskripsi' => 'nullable',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $produk = Produk::findOrFail($id);

        $data = $request->only([
            'kode_barang',
            'nama_barang',
            'kategori_id',
            'supplier_id',
            'harga_beli',
            'persentase_keuntungan',
            'deskripsi',
        ]);

        $data['satuan'] = 'pcs';

        if ($request->hasFile('gambar')) {
            if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
                Storage::disk('public')->delete($produk->gambar);
            }

            $file = $request->file('gambar');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('produk', $fileName, 'public');

            $data['gambar'] = $filePath;
        }

        $produk->update($data);

        return redirect()->back()->with('success', 'Produk berhasil diperbarui!');
    }

    /**
     * Menghapus produk berdasarkan ID produk.
     *
     * Fungsi ini menghapus produk berdasarkan ID yang diberikan, beserta gambar yang terhubung.
     * Setelah penghapusan, redirect ke halaman daftar produk.
     *
     * @param int $id ID produk yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman daftar produk dengan pesan sukses
     */
    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);

        if ($produk->gambar) {
            Storage::disk('public')->delete($produk->gambar);
        }

        // Hapus produk dari database
        $produk->delete();

        return redirect()->back()->with('success', 'Produk berhasil dihapus!');
    }

    /**
     * Mengimpor data produk dari file Excel.
     *
     * Fungsi ini mengimpor data produk dari file Excel yang diunggah oleh pengguna dan memproses
     * data tersebut untuk disimpan ke dalam database. Setelah proses import selesai,
     * redirect ke halaman sebelumnya dengan pesan sukses.
     *
     * @param \Illuminate\Http\Request $request Data yang diterima dari form upload file
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman sebelumnya dengan pesan sukses
     */
    public function import(Request $request)
    {
        Excel::import(new ProdukImport, $request->file('file'));

        return redirect()->back()->with('success', 'Data produk berhasil diimport!');
    }
}
