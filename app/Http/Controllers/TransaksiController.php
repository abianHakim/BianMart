<?php

namespace App\Http\Controllers;

use App\Models\BatchStok;
use App\Models\DetailPenjualan;
use App\Models\LoyaltyPoint;
use App\Models\Member;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransaksiController extends Controller
{
    public function riwayat()
    {
        $riwayat = Penjualan::with(['detail.produk', 'user', 'member'])
            ->latest()
            ->get();

        return view('admin.transaksi.riwayat', compact('riwayat'));
    }


    public function index()
    {
        return view('admin.transaksi.kasir');
    }


    public function getBarang($kode)
    {
        $barang = Produk::where('kode_barang', $kode)->first();

        if (!$barang) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Barang tidak ditemukan!',
            ], 404);
        }

        // Ambil stok berdasarkan FIFO (batch stok tertua)
        $stokTersedia = BatchStok::where('produk_id', $barang->id)
            ->where('stok_toko', '>', 0)
            ->orderBy('expired_date', 'asc')
            ->sum('stok_toko');

        if ($stokTersedia <= 0) {
            return response()->json([
                'status' => 'out_of_stock',
                'message' => 'Barang ini tidak tersedia di toko!',
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'id' => $barang->id,
            'nama_barang' => $barang->nama_barang,
            'harga_jual' => $barang->harga_jual,
            'stok_toko' => $stokTersedia
        ]);
    }

    public function cariMember(Request $request)
    {
        $no_telp = $request->input('no_telp');

        // Cari member berdasarkan no_telp
        $member = Member::where('no_telp', $no_telp)->first();

        if (!$member) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Member tidak ditemukan.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'id_member' => $member->id, // Kirim ID member
            'nama' => $member->nama
        ]);
    }

    private function syncStokBarang($produkId)
    {
        $totalStokToko = BatchStok::where('produk_id', $produkId)->sum('stok_toko');

        StokBarang::where('produk_id', $produkId)->update([
            'stok_toko' => $totalStokToko
        ]);

        Log::info("Sync Stok Barang: Produk ID {$produkId}, Sisa Stok Toko: {$totalStokToko}");
    }

    public function show($id)
    {
        $penjualan = Penjualan::with(['detail.produk', 'user', 'member'])->findOrFail($id);

        if (!$penjualan->detail || $penjualan->detail->isEmpty()) {
            return response()->json([
                'error' => 'Detail transaksi tidak ditemukan!',
                'penjualan' => $penjualan
            ]);
        }

        // Cek apakah uang_pelanggan kosong atau NULL, beri nilai default 0
        $totalBayar = round($penjualan->total_bayar, 2); // Pembulatan ke dua angka desimal
        $uangPelanggan = round($penjualan->uang_pelanggan ?? 0, 2); // Pastikan ada nilai default 0
        $kembalian = round($uangPelanggan - $totalBayar, 2); // Menghitung kembalian

        return response()->json([
            'no_faktur' => $penjualan->no_faktur,
            'tgl_faktur' => \Carbon\Carbon::parse($penjualan->tgl_faktur)->format('d-m-Y'),
            'total_bayar' => $totalBayar,
            'uang_pelanggan' => $penjualan->uang_pelanggan,
            'kembalian' => $kembalian,
            'user' => [
                'id' => $penjualan->user->id,
                'name' => $penjualan->user->name,
            ],
            'member' => $penjualan->member ? [
                'id' => $penjualan->member->id,
                'nama' => $penjualan->member->nama,
                'kode_member' => $penjualan->member->kode_member,
            ] : null,
            'detail_penjualan' => $penjualan->detail->map(function ($detail) {
                return [
                    'produk_id' => $detail->produk ? $detail->produk->id : null,
                    'nama_produk' => $detail->produk ? $detail->produk->nama_barang : 'Produk tidak ditemukan',
                    'harga_jual' => $detail->harga_jual,
                    'jumlah' => $detail->jumlah,
                    'sub_total' => round($detail->sub_total, 2),
                ];
            })
        ]);
    }




    public function store(Request $request)
    {
        try {
            Log::info('Data diterima di backend:', $request->all());

            // Validasi Data
            $request->validate([
                'cart' => 'required|array|min:1',
                'cart.*.id' => 'required|exists:produk,id',
                'cart.*.qty' => 'required|integer|min:1'
            ]);

            DB::beginTransaction(); // Mulai transaksi

            $totalHarga = 0;
            $items = [];

            // Generate Nomor Faktur
            $tanggalSekarang = now()->format('Ymd');
            $jumlahHariIni = Penjualan::whereDate('tgl_faktur', now()->toDateString())->count() + 1;
            $noFaktur = 'INV-' . $tanggalSekarang . '-' . str_pad($jumlahHariIni, 4, '0', STR_PAD_LEFT);

            // Periksa Stok Barang di Toko
            foreach ($request->cart as $item) {
                $stokTersedia = BatchStok::where('produk_id', $item['id'])
                    ->where('stok_toko', '>', 0)
                    ->sum('stok_toko');

                if ($stokTersedia < $item['qty']) {
                    return response()->json([
                        'message' => "Stok barang tidak mencukupi untuk Produk ID {$item['id']}!"
                    ], 400);
                }
            }

            // Simpan Transaksi ke Tabel `penjualan`
            $penjualan = Penjualan::create([
                'no_faktur' => $noFaktur,
                'tgl_faktur' => now(),
                'total_bayar' => 0, // Akan dihitung ulang
                'uang_pelanggan' => $request->uang_pelanggan ?? null,
                'member_id' => $request->member_id ?? null,
                'user_id' => Auth::id(),
                'metode_pembayaran' => 'cash',
                'status' => 'selesai'
            ]);

            // FIFO: Ambil Stok Barang dari Batch Stok Tertua
            foreach ($request->cart as $item) {
                $produk = Produk::where('id', $item['id'])->first();
                if (!$produk) {
                    return response()->json([
                        'message' => "Produk dengan ID {$item['id']} tidak ditemukan!"
                    ], 400);
                }

                // Hitung harga jual
                $persentaseKeuntungan = $produk->persentase_keuntungan ?? 0;
                $hargaJual = $produk->harga_beli + ($produk->harga_beli * ($persentaseKeuntungan / 100));

                // Konversi qty ke integer
                $jumlahDibutuhkan = (int) $item['qty'];
                $subTotal = $hargaJual * $jumlahDibutuhkan;
                $totalHarga += $subTotal;

                // Ambil batch stok berdasarkan FIFO (expired date terdekat)
                $batchList = BatchStok::where('produk_id', $item['id'])
                    ->where('stok_toko', '>', 0)
                    ->orderBy('expired_date', 'asc')
                    ->get();

                $stokDiambilTotal = 0;

                foreach ($batchList as $batch) {
                    if ($jumlahDibutuhkan <= 0) break;

                    $stokTersedia = $batch->stok_toko;
                    $stokDiambil = min($stokTersedia, $jumlahDibutuhkan);

                    // Kurangi stok toko di batch ini
                    $batch->stok_toko -= $stokDiambil;
                    $batch->save();

                    Log::info("Batch Stok Diperbarui", [
                        'batch_id' => $batch->id,
                        'produk_id' => $batch->produk_id,
                        'stok_toko_sebelum' => $stokTersedia,
                        'stok_diambil' => $stokDiambil,
                        'stok_toko_sesudah' => $batch->stok_toko
                    ]);

                    $stokDiambilTotal += $stokDiambil;
                    $jumlahDibutuhkan -= $stokDiambil;
                }

                // Update total stok di tabel `stok_barang` setelah FIFO berjalan
                $this->syncStokBarang($item['id']);

                // Simpan Detail Penjualan
                $items[] = [
                    'penjualan_id' => $penjualan->id,
                    'produk_id' => $item['id'],
                    'harga_jual' => $hargaJual,
                    'jumlah' => (int) $item['qty'],
                    'diskon' => 0,
                    'sub_total' => $subTotal
                ];
            }

            // Simpan ke Tabel `detail_penjualan`
            DetailPenjualan::insert($items);

            // Update Total Pembayaran di `penjualan`
            $penjualan->update(['total_bayar' => round($totalHarga, 2)]); // Pembulatan total bayar

            // Hitung Loyalty Points
            if ($request->member_id) {
                $poinDidapat = floor($totalHarga / 1000);

                if ($poinDidapat > 0) {
                    LoyaltyPoint::create([
                        'member_id' => $request->member_id,
                        'penjualan_id' => $penjualan->id,
                        'point_didapat' => $poinDidapat,
                    ]);
                }
            }

            DB::commit(); // Simpan transaksi ke database

            // Render invoice HTML untuk pengiriman ke frontend
            $invoiceHtml = view('admin.transaksi.invoice', compact('penjualan', 'items'))->render();

            return response()->json([
                'message' => 'Transaksi berhasil!',
                'no_faktur' => $noFaktur,
                'total_harga' => round($totalHarga, 2),
                'invoice_html' => $invoiceHtml,
                'id_transaksi' => $penjualan->id,
                'uang_pelanggan' => $request->uang_pelanggan, 
                'kembalian' => ($request->uang_pelanggan ?? 0) - $totalHarga 
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan jika ada error
            return response()->json([
                'message' => 'Terjadi kesalahan!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
