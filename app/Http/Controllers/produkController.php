<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class produkController extends Controller
{
    public function index()
    {
        $produk = Produk::with('kategori')->get();
        $kategori = KategoriProduk::all();
        return view('admin.manajemenProduk.produk', compact('produk', 'kategori'));
    }

    private function generateKodeBarang()
    {
        $lastProduct = Produk::latest()->first();
        $lastNumber = $lastProduct ? (int)substr($lastProduct->kode_barang, 3) : 0;
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        return 'PRD' . $newNumber;
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required',
            'kategori_id' => 'required',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        // dd($request->all());

        $kode_barang = $this->generateKodeBarang();

        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('produk', $imageName, 'public');
        } else {
            $path = null;
        }

        // Simpan ke database
        Produk::create([
            'kode_barang' => $kode_barang,
            'nama_barang' => $request->nama_barang,
            'kategori_id' => $request->kategori_id,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'deskripsi' => $request->deskripsi,
            'satuan' => $request->satuan,
            'gambar' => $path,
        ]);


        return redirect()->back()->with('success', 'Produk berhasil ditambahkan!');
    }



    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_barang' => 'required',
            'nama_barang' => 'required',
            'kategori_id' => 'required',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'deskripsi' => 'nullable',
            'satuan' => 'nullable',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $produk = Produk::findOrFail($id);

        $data = $request->only([
            'kode_barang',
            'nama_barang',
            'kategori_id',
            'harga_beli',
            'harga_jual',
            'deskripsi',
            'satuan'
        ]);

        if ($request->hasFile('gambar')) {
            if ($produk->gambar) {
                Storage::delete('public/' . $produk->gambar);
            }

            $file = $request->file('gambar');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/produk', $fileName);

            $data['gambar'] = 'produk/' . $fileName;
        }

        $produk->update($data);

        return redirect()->back()->with('success', 'Produk berhasil diperbarui!');
    }


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
}
