<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\StokBarang;
use App\Models\BatchStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockBarangController extends Controller
{
    /**
     * Menampilkan daftar stok barang.
     *
     * Fungsi ini mengambil data stok barang dan produk untuk ditampilkan di halaman 
     * manajemen stok barang. Produk digunakan untuk dropdown dalam modal.
     *
     * @return \Illuminate\View\View Tampilan daftar stok barang
     */
    public function index()
    {
        $stock = StokBarang::with('produk')->get();
        $produk = Produk::all(); // Untuk dropdown produk dalam modal
        return view("admin.manajemenStok.stokBarang", compact("stock", "produk"));
    }

    /**
     * Menambahkan stok barang baru.
     *
     * Fungsi ini menerima input dari form untuk menambahkan stok barang baru. Data
     * akan divalidasi terlebih dahulu, kemudian disimpan ke dalam tabel batch_stok.
     * Setelah itu, stok barang akan disinkronkan untuk memperbarui total stok.
     *
     * @param \Illuminate\Http\Request $request Data yang dikirimkan dari form untuk menambahkan stok barang
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman daftar stok barang dengan pesan sukses atau error
     */
    public function store(Request $request)
    {
        $request->validate([
            'produk_id'    => 'required|exists:produk,id',
            'stok_gudang'  => 'required|integer|min:0',
            'stok_toko'    => 'required|integer|min:0',
            'expired_date' => 'nullable|date|after:today',
        ]);

        // Menyimpan di batch stok terlebih dahulu
        DB::beginTransaction();
        try {
            // Generate kode batch yang unik
            $latestBatchCount = BatchStok::whereYear('created_at', now()->year)->count() + 1;
            $kode_batch = 'B-' . now()->format('y') . '-' . $request->produk_id . '-' . str_pad($latestBatchCount, 2, '0', STR_PAD_LEFT);

            // Menyimpan stok ke batch stok
            BatchStok::create([
                'produk_id'    => $request->produk_id,
                'kode_batch'   => $kode_batch,
                'expired_date' => $request->expired_date ?? now()->addMonths(6), // Default 6 bulan jika tidak diisi
                'stok_gudang'  => $request->stok_gudang,
                'stok_toko'    => $request->stok_toko,
            ]);

            // Sinkronisasi total stok
            $this->syncStokBarang($request->produk_id);

            DB::commit();
            return redirect()->route('stokbarang.index')->with('success', 'Stok berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('stokbarang.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui data stok barang.
     *
     * Fungsi ini menerima input dari form untuk memperbarui stok barang yang sudah ada.
     * Tergantung mode (add/replace), stok dapat ditambahkan atau digantikan dengan
     * batch baru. Setelah itu, stok barang akan disinkronkan untuk memperbarui total stok.
     *
     * @param \Illuminate\Http\Request $request Data yang dikirimkan dari form untuk memperbarui stok barang
     * @param int $id ID stok barang yang akan diperbarui
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman daftar stok barang dengan pesan sukses atau error
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'stok_gudang'  => 'required|integer|min:0',
            'stok_toko'    => 'required|integer|min:0',
            'mode'         => 'required|in:add,replace',
            'expired_date' => 'nullable|date|after:today',
        ]);

        $stokbarang = StokBarang::findOrFail($id);

        DB::beginTransaction();
        try {
            if ($request->mode == 'replace') {
                // Hapus stok lama dari batch_stok dan buat batch baru
                BatchStok::where('produk_id', $stokbarang->produk_id)->delete();

                $latestBatchCount = BatchStok::whereYear('created_at', now()->year)->count() + 1;
                $kode_batch = 'B-' . now()->format('y') . '-' . $stokbarang->produk_id . '-' . str_pad($latestBatchCount, 2, '0', STR_PAD_LEFT);

                BatchStok::create([
                    'produk_id'    => $stokbarang->produk_id,
                    'kode_batch'   => $kode_batch,
                    'expired_date' => $request->expired_date ?? now()->addMonths(6),
                    'stok_gudang'  => $request->stok_gudang,
                    'stok_toko'    => $request->stok_toko,
                ]);
            } else {
                // Mode add: Tambahkan stok ke batch terbaru
                $latestBatch = BatchStok::where('produk_id', $stokbarang->produk_id)->latest('id')->first();

                if ($latestBatch) {
                    $latestBatch->increment('stok_gudang', $request->stok_gudang);
                    $latestBatch->increment('stok_toko', $request->stok_toko);
                } else {
                    // Jika tidak ada batch sebelumnya, buat batch baru
                    $latestBatchCount = BatchStok::whereYear('created_at', now()->year)->count() + 1;
                    $kode_batch = 'B-' . now()->format('y') . '-' . $stokbarang->produk_id . '-' . str_pad($latestBatchCount, 2, '0', STR_PAD_LEFT);

                    BatchStok::create([
                        'produk_id'    => $stokbarang->produk_id,
                        'kode_batch'   => $kode_batch,
                        'expired_date' => $request->expired_date ?? now()->addMonths(6),
                        'stok_gudang'  => $request->stok_gudang,
                        'stok_toko'    => $request->stok_toko,
                    ]);
                }
            }

            // Sinkronisasi total stok
            $this->syncStokBarang($stokbarang->produk_id);

            DB::commit();
            return redirect()->route('stokbarang.index')->with('success', 'Stok berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('stokbarang.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data stok barang.
     *
     * Fungsi ini menghapus stok barang berdasarkan ID yang diberikan beserta batch stok
     * yang berhubungan. Setelah penghapusan, redirect ke halaman daftar stok barang.
     *
     * @param int $id ID stok barang yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman daftar stok barang dengan pesan sukses atau error
     */
    public function destroy($id)
    {
        $stok = StokBarang::findOrFail($id);

        DB::beginTransaction();
        try {
            // Hapus semua batch stok yang berhubungan
            BatchStok::where('produk_id', $stok->produk_id)->delete();

            // Hapus stok barang
            $deleted = $stok->delete();

            DB::commit();

            if ($deleted) {
                return redirect()->route('stokbarang.index')->with('success', 'Stok berhasil dihapus!');
            } else {
                return redirect()->route('stokbarang.index')->with('error', 'Stok gagal dihapus!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('stokbarang.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menyinkronkan stok barang dengan batch stok.
     *
     * Fungsi ini menghitung total stok barang yang tersedia di gudang dan toko 
     * berdasarkan data batch_stok dan kemudian memperbarui entri stok_barang.
     *
     * @param int $produk_id ID produk yang stoknya perlu disinkronkan
     */
    private function syncStokBarang($produk_id)
    {
        // Hitung total stok dari batch_stok
        $totalStokGudang = BatchStok::where('produk_id', $produk_id)->sum('stok_gudang');
        $totalStokToko = BatchStok::where('produk_id', $produk_id)->sum('stok_toko');

        // Update atau buat entri di stok_barang
        StokBarang::updateOrCreate(
            ['produk_id' => $produk_id],
            ['stok_gudang' => $totalStokGudang, 'stok_toko' => $totalStokToko]
        );
    }
}
