<?php

namespace App\Http\Controllers;

use App\Models\BatchStok;
use App\Models\MutasiStok;
use App\Models\StokBarang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class barangDisplayController extends Controller
{
    public function index()
    {
        $stokBarang = StokBarang::where('stok_toko', '>', 0)->get();
        return view('admin.barangDisplay.displayBarang', compact('stokBarang'));
    }

    public function mutasiIndex(Request $request)
    {
        $query = MutasiStok::query();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
            $endDate = Carbon::parse($request->end_date)->format('Y-m-d');
            $query->whereBetween('tgl_mutasi', [$startDate, $endDate]);
        }

        $stokBarang = StokBarang::with('produk')->get();
        $historyMutasi = $query->with('produk')->orderBy('tgl_mutasi', 'asc')->get();

        return view('admin.barangDisplay.mutasiStok', compact('stokBarang', 'historyMutasi'));
    }


    public function prosesMutasi(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'barang_id' => 'required|array',
            'barang_id.*' => 'exists:stok_barang,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
            'tipe_mutasi' => 'required|array',
            'tipe_mutasi.*' => 'in:gudang_ke_toko,toko_ke_gudang',
            'keterangan' => 'nullable|string|max:255'
        ]);

        $keterangan = $request->keterangan;

        foreach ($request->barang_id as $index => $barang_id) {
            $barang = StokBarang::findOrFail($barang_id);
            $jumlah = $request->jumlah[$index];
            $tipe = $request->tipe_mutasi[$index];

            // Validasi stok sebelum mutasi
            if ($tipe == 'gudang_ke_toko' && $barang->stok_gudang < $jumlah) {
                return back()->with('error', 'Stok di Gudang tidak mencukupi untuk salah satu barang!');
            }
            if ($tipe == 'toko_ke_gudang' && $barang->stok_toko < $jumlah) {
                return back()->with('error', 'Stok di Toko tidak mencukupi untuk salah satu barang!');
            }

            // Ambil batch stok sesuai tipe mutasi
            $batchStok = BatchStok::where('produk_id', $barang->produk_id)
                ->where(function ($query) use ($tipe) {
                    if ($tipe == 'gudang_ke_toko') {
                        $query->where('stok_gudang', '>', 0);
                    } else {
                        $query->where('stok_toko', '>', 0);
                    }
                })
                ->orderBy('expired_date', $tipe == 'gudang_ke_toko' ? 'asc' : 'desc')
                ->get();

            $sisaJumlah = $jumlah;

            // Proses mutasi berdasarkan batch stok
            foreach ($batchStok as $batch) {
                if ($sisaJumlah <= 0) break;

                if ($tipe == 'gudang_ke_toko') {
                    if ($batch->stok_gudang >= $sisaJumlah) {
                        $batch->stok_gudang -= $sisaJumlah;
                        $batch->stok_toko += $sisaJumlah;
                        $sisaJumlah = 0;
                    } else {
                        $sisaJumlah -= $batch->stok_gudang;
                        $batch->stok_toko += $batch->stok_gudang;
                        $batch->stok_gudang = 0;
                    }
                } else {
                    if ($batch->stok_toko >= $sisaJumlah) {
                        $batch->stok_toko -= $sisaJumlah;
                        $batch->stok_gudang += $sisaJumlah;
                        $sisaJumlah = 0;
                    } else {
                        $sisaJumlah -= $batch->stok_toko;
                        $batch->stok_gudang += $batch->stok_toko;
                        $batch->stok_toko = 0;
                    }
                }

                $batch->save();
            }

            // Update stok utama
            if ($tipe == 'gudang_ke_toko') {
                $barang->stok_gudang -= $jumlah;
                $barang->stok_toko += $jumlah;
                $dariLokasi = 'gudang';
                $keLokasi = 'toko';
            } else {
                $barang->stok_toko -= $jumlah;
                $barang->stok_gudang += $jumlah;
                $dariLokasi = 'toko';
                $keLokasi = 'gudang';
            }

            $barang->save();

            // Simpan data mutasi stok
            MutasiStok::create([
                'user_id' => Auth::id(),
                'produk_id' => $barang->produk_id,
                'dari_lokasi' => $dariLokasi, // Gunakan enum 'gudang' atau 'toko'
                'ke_lokasi' => $keLokasi,
                'jumlah' => $jumlah,
                'tgl_mutasi' => now(),
                'keterangan' => $keterangan
            ]);
        }

        return redirect()->back()->with('success', 'Mutasi stok berhasil diproses!');
    }

    // Fungsi untuk update lokasi batch stok
    private function updateBatchLokasi($batchId, $jumlah, $tipe)
    {
        $batch = BatchStok::find($batchId);

        if ($batch) {
            // Update lokasi batch ke tujuan mutasi
            BatchStok::create([
                'produk_id' => $batch->produk_id,
                'jumlah' => $jumlah,
                'tanggal_expired' => $batch->tanggal_expired,
                'lokasi' => $tipe == 'gudang_ke_toko' ? 'toko' : 'gudang',
            ]);
        }
    }
}
