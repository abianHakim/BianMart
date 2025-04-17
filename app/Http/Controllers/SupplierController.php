<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Menampilkan daftar supplier.
     *
     * Fungsi ini mengambil semua data supplier yang ada dan mengirimkannya ke tampilan 
     * untuk ditampilkan di halaman manajemen supplier.
     *
     * @return \Illuminate\View\View Tampilan daftar supplier
     */
    public function index()
    {
        $suppliers = Supplier::all();
        return view('admin.manajemenStok.supplier', compact('suppliers'));
    }

    /**
     * Menambahkan supplier baru.
     *
     * Fungsi ini menerima input dari form untuk menambahkan supplier baru ke database.
     * Sebelum menyimpan, data akan divalidasi, dan jika valid, supplier akan ditambahkan
     * ke tabel supplier.
     *
     * @param \Illuminate\Http\Request $request Data yang dikirimkan dari form untuk menambahkan supplier
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman daftar supplier dengan pesan sukses
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required',
            'telepon' => 'required',
            'email' => 'nullable|email',
            'alamat' => 'nullable',
        ]);

        Supplier::create([
            'nama_supplier' => $request->nama_supplier,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    /**
     * Memperbarui data supplier.
     *
     * Fungsi ini menerima input dari form untuk memperbarui data supplier yang ada.
     * Data supplier yang akan diperbarui divalidasi terlebih dahulu, dan kemudian
     * disimpan kembali ke database.
     *
     * @param \Illuminate\Http\Request $request Data yang dikirimkan dari form untuk memperbarui supplier
     * @param int $id ID supplier yang akan diperbarui
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman daftar supplier dengan pesan sukses
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_supplier' => 'required',
            'telepon' => 'required',
            'email' => 'nullable|email',
            'alamat' => 'nullable',
        ]);

        $supplier = Supplier::findOrFail($id);

        $supplier->update([
            'nama_supplier' => $request->nama_supplier,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    /**
     * Menghapus data supplier.
     *
     * Fungsi ini menghapus supplier berdasarkan ID yang diberikan dari database.
     * Setelah penghapusan, akan ada redirect ke halaman daftar supplier dengan pesan sukses.
     *
     * @param int $id ID supplier yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman daftar supplier dengan pesan sukses
     */
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
