<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\StokBarang;
use Illuminate\Http\Request;

class stockBarangController extends Controller
{
    public function index()
    {
        $stock = StokBarang::with('produk')->get();
        $produk = Produk::all(); // Untuk dropdown produk dalam modal
        return view("admin.manajemenStok.stokBarang", compact("stock", "produk"));
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id'   => 'required|exists:produk,id',
            'stok_gudang' => 'required|integer|min:0',
            'stok_toko'   => 'required|integer|min:0',
        ]);

        $stokBarang = StokBarang::where('produk_id', $request->produk_id)->first();

        if ($stokBarang) {
            $stokBarang->increment('stok_gudang', $request->stok_gudang);
            $stokBarang->increment('stok_toko', $request->stok_toko);
        } else {
            StokBarang::create($request->only('produk_id', 'stok_gudang', 'stok_toko'));
        }

        return redirect()->route('stokbarang.index')->with('success', 'Stok berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $stokbarang = StokBarang::findOrFail($id);

        $request->validate([
            'stok_gudang' => 'required|integer|min:0',
            'stok_toko'   => 'required|integer|min:0',
            'mode'        => 'required|in:add,replace',
        ]);

        if ($request->mode == 'replace') {
            $stokbarang->update([
                'stok_gudang' => $request->stok_gudang,
                'stok_toko'   => $request->stok_toko,
            ]);
        } else {
            $stokbarang->increment('stok_gudang', $request->stok_gudang);
            $stokbarang->increment('stok_toko', $request->stok_toko);
        }

        return redirect()->route('stokbarang.index')->with('success', 'Stok berhasil diperbarui!');
    }


    public function destroy($id)
    {
        $stok = StokBarang::findOrFail($id);
        $stok->delete();

        return redirect()->route('stokbarang.index')->with('success', 'Stok berhasil dihapus!');
    }
}
