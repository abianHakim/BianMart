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

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class TransaksiController extends Controller
{
    /**
     * Menampilkan riwayat transaksi penjualan.
     *
     * Fungsi ini mengambil semua transaksi penjualan yang telah selesai, termasuk detail produk,
     * pengguna (kasir), dan informasi member yang terlibat dalam transaksi tersebut.
     * Data transaksi akan ditampilkan dalam urutan terbaru dan dikirimkan ke tampilan 'riwayat' di admin panel.
     *
     * @return \Illuminate\View\View Tampilan yang menampilkan riwayat transaksi penjualan
     */
    public function riwayat()
    {
        $riwayat = Penjualan::with(['detail.produk', 'user', 'member'])
            ->latest()
            ->get();

        return view('admin.transaksi.riwayat', compact('riwayat'));
    }

    /**
     * Menampilkan halaman kasir.
     *
     * Fungsi ini menampilkan halaman kasir pada panel admin, yang memungkinkan kasir untuk 
     * melakukan transaksi penjualan. Halaman ini merupakan halaman utama bagi kasir untuk memulai transaksi.
     *
     * @return \Illuminate\View\View Tampilan halaman kasir
     */
    public function index()
    {
        return view('admin.transaksi.kasir');
    }


    /**
     * Mendapatkan informasi barang berdasarkan kode barang.
     *
     * Fungsi ini mencari barang berdasarkan kode yang diberikan. Jika barang ditemukan, 
     * informasi mengenai nama barang, harga jual, dan stok toko akan dikembalikan. 
     * Jika barang tidak ditemukan atau stok tidak tersedia, respons error akan dikembalikan.
     *
     * @param string $kode Kode barang yang dicari
     * @return \Illuminate\Http\JsonResponse JSON response yang berisi data barang atau error
     */
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

    /**
     * Mencari member berdasarkan nomor telepon.
     *
     * Fungsi ini mencari member berdasarkan nomor telepon yang diberikan. Jika member ditemukan, 
     * ID dan nama member akan dikembalikan. Jika tidak ditemukan, respons error akan dikembalikan.
     *
     * @param \Illuminate\Http\Request $request Request yang berisi input nomor telepon
     * @return \Illuminate\Http\JsonResponse JSON response yang berisi data member atau error
     */
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

    /**
     * Sinkronisasi stok barang berdasarkan produk ID.
     *
     * Fungsi ini menghitung total stok yang tersedia untuk produk berdasarkan ID yang diberikan,
     * kemudian mengupdate nilai stok toko di tabel `StokBarang` sesuai dengan total stok yang ada.
     * Log aktivitas sinkronisasi juga dicatat untuk keperluan audit.
     *
     * @param int $produkId ID produk yang stoknya akan disinkronkan
     */
    private function syncStokBarang($produkId)
    {
        $totalStokToko = BatchStok::where('produk_id', $produkId)->sum('stok_toko');

        StokBarang::where('produk_id', $produkId)->update([
            'stok_toko' => $totalStokToko
        ]);

        Log::info("Sync Stok Barang: Produk ID {$produkId}, Sisa Stok Toko: {$totalStokToko}");
    }


    /**
     * Mendapatkan transaksi penjualan terakhir yang sudah selesai.
     * 
     * Fungsi ini mengembalikan transaksi terakhir dengan status 'selesai', 
     * termasuk detail transaksi, produk yang dibeli, informasi kasir, 
     * dan data member jika tersedia. Jika tidak ada transaksi yang ditemukan, 
     * maka akan mengembalikan respons error.
     *
     * @return \Illuminate\Http\JsonResponse JSON response yang berisi data transaksi atau error
     */
    public function getTransaksiTerakhir()
    {
        try {
            $transaksi = Penjualan::with(['detailTransaksi.produk', 'user', 'member'])
                ->where('status', 'selesai')
                ->latest()
                ->first();

            if (!$transaksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Belum ada transaksi'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'no_faktur' => $transaksi->no_faktur,
                    'tanggal' => $transaksi->created_at->toIso8601String(),
                    'kasir' => $transaksi->user->name,
                    'member' => $transaksi->member ? $transaksi->member->nama : '-',
                    'items' => $transaksi->detailTransaksi->map(function ($item) {
                        return [
                            'produk' => $item->produk->nama_barang ?? 'Produk dihapus',
                            'qty' => $item->jumlah,
                            'subtotal' => $item->sub_total
                        ];
                    }),
                    'total' => [
                        'qty' => $transaksi->detailTransaksi->sum('jumlah'),
                        'amount' => $transaksi->total_bayar,
                        'paid' => $transaksi->uang_pelanggan,
                        'change' => $transaksi->kembalian
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail transaksi penjualan berdasarkan ID.
     *
     * Fungsi ini mengambil data transaksi penjualan berdasarkan ID yang diberikan,
     * serta menampilkan detail transaksi, informasi produk yang dibeli, 
     * informasi kasir, informasi member, dan perhitungan kembalian. 
     * Jika detail transaksi tidak ditemukan, maka akan mengembalikan respons error.
     *
     * @param int $id ID transaksi yang akan ditampilkan
     * @return \Illuminate\Http\JsonResponse JSON response yang berisi detail transaksi atau error
     */
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


    /**
     * Menyimpan transaksi penjualan
     *
     * Fungsi ini akan menyimpan transaksi penjualan, memvalidasi data input dari 
     * pengguna, memeriksa ketersediaan stok barang, menghitung total harga, 
     * memproses transaksi dengan sistem FIFO untuk stok, mencatat loyalty points 
     * jika member terdaftar, mencetak struk, dan mengembalikan response JSON yang 
     * berisi informasi transaksi.
     *
     * @param \Illuminate\Http\Request $request Request yang berisi data dari frontend, 
     * berisi informasi tentang barang yang dibeli, jumlah yang dibeli, dan uang 
     * yang diberikan pelanggan.
     * 
     * @return \Illuminate\Http\JsonResponse Response JSON berisi pesan sukses, 
     * nomor faktur, total harga, invoice dalam bentuk HTML, dan informasi transaksi.
     * 
     * @throws \Exception Jika terjadi error dalam proses transaksi, transaksi 
     * akan dibatalkan dan response error dikembalikan.
     */
    public function store(Request $request)
    {
        try {
            Log::info('Data diterima di backend:', $request->all()); // Log data yang diterima

            // Validasi input data dari frontend
            $request->validate([
                'cart' => 'required|array|min:1', // Cart harus berupa array dan tidak kosong
                'cart.*.id' => 'required|exists:produk,id', // Setiap item harus ada di tabel produk
                'cart.*.qty' => 'required|integer|min:1' // Setiap qty harus berupa angka dan lebih dari 0
            ]);

            DB::beginTransaction(); // Memulai transaksi database

            $totalHarga = 0; // Inisialisasi total harga
            $items = []; // Menyimpan detail barang yang dibeli

            // Generate Nomor Faktur berdasarkan tanggal dan urutan penjualan hari ini
            $tanggalSekarang = now()->format('Ymd');
            $jumlahHariIni = Penjualan::whereDate('tgl_faktur', now()->toDateString())->count() + 1;
            $noFaktur = 'INV-' . $tanggalSekarang . '-' . str_pad($jumlahHariIni, 4, '0', STR_PAD_LEFT);

            // Periksa ketersediaan stok untuk setiap item dalam cart
            foreach ($request->cart as $item) {
                $stokTersedia = BatchStok::where('produk_id', $item['id'])
                    ->where('stok_toko', '>', 0)
                    ->sum('stok_toko');

                if ($stokTersedia < $item['qty']) {
                    return response()->json([
                        'message' => "Stok barang tidak mencukupi untuk Produk ID {$item['id']}!"
                    ], 400); // Jika stok tidak mencukupi, kirimkan pesan error
                }
            }

            // Simpan data transaksi ke tabel penjualan
            $penjualan = Penjualan::create([
                'no_faktur' => $noFaktur,
                'tgl_faktur' => now(),
                'total_bayar' => 0, // Total bayar akan dihitung setelah proses
                'uang_pelanggan' => $request->uang_pelanggan ?? null,
                'member_id' => $request->member_id ?? null,
                'user_id' => Auth::id(),
                'metode_pembayaran' => 'cash',
                'status' => 'selesai'
            ]);

            // FIFO: Mengambil stok barang dari batch yang tertua
            foreach ($request->cart as $item) {
                $produk = Produk::where('id', $item['id'])->first();
                if (!$produk) {
                    return response()->json([
                        'message' => "Produk dengan ID {$item['id']} tidak ditemukan!"
                    ], 400); // Jika produk tidak ditemukan, kirimkan pesan error
                }

                // Menghitung harga jual berdasarkan harga beli dan persentase keuntungan
                $persentaseKeuntungan = $produk->persentase_keuntungan ?? 0;
                $hargaJual = $produk->harga_beli + ($produk->harga_beli * ($persentaseKeuntungan / 100));

                // Hitung subtotal berdasarkan jumlah barang yang dibeli
                $jumlahDibutuhkan = (int) $item['qty'];
                $subTotal = $hargaJual * $jumlahDibutuhkan;
                $totalHarga += $subTotal;

                // Ambil batch stok yang tersedia untuk produk tersebut berdasarkan FIFO (expired date terdekat)
                $batchList = BatchStok::where('produk_id', $item['id'])
                    ->where('stok_toko', '>', 0)
                    ->orderBy('expired_date', 'asc')
                    ->get();

                $stokDiambilTotal = 0;

                // Ambil stok barang dari setiap batch
                foreach ($batchList as $batch) {
                    if ($jumlahDibutuhkan <= 0) break; // Jika sudah memenuhi kebutuhan, keluar dari loop

                    $stokTersedia = $batch->stok_toko;
                    $stokDiambil = min($stokTersedia, $jumlahDibutuhkan);

                    // Kurangi stok yang diambil dari batch
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

                // Update stok barang setelah transaksi FIFO
                $this->syncStokBarang($item['id']);

                // Simpan detail penjualan untuk setiap item
                $items[] = [
                    'penjualan_id' => $penjualan->id,
                    'produk_id' => $item['id'],
                    'harga_jual' => $hargaJual,
                    'jumlah' => (int) $item['qty'],
                    'diskon' => 0,
                    'sub_total' => $subTotal
                ];
            }

            // Simpan detail penjualan ke tabel `detail_penjualan`
            DetailPenjualan::insert($items);

            // Update total bayar transaksi
            $penjualan->update(['total_bayar' => round($totalHarga, 2)]);

            // Hitung dan simpan loyalty points jika member terdaftar
            if ($request->member_id) {
                $poinDidapat = floor($totalHarga / 20000) * 3; // Setiap kelipatan 20.000 mendapat 3 poin

                if ($poinDidapat > 0) {
                    LoyaltyPoint::create([
                        'member_id' => $request->member_id,
                        'penjualan_id' => $penjualan->id,
                        'point_didapat' => $poinDidapat,
                    ]);
                }
            }

            DB::commit(); // Commit transaksi ke database

            // Mencetak struk transaksi menggunakan printer POS
            try {
                $connector = new WindowsPrintConnector("POS-51");
                $printer = new Printer($connector);

                // Cetak header struk
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("==============================\n");
                $printer->text("Bian Mart\n");
                $printer->text("Struk Pembelian\n");
                $printer->text("==============================\n\n");

                // Cetak informasi transaksi
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printInfoLine = function ($label, $value) use ($printer) {
                    $printer->text(str_pad($label . ":", 12) . " " . $value . "\n");
                };
                $printInfoLine("No Faktur", $noFaktur);
                $printInfoLine("Tanggal", now()->format('d-m-Y'));
                $printInfoLine("Kasir", Auth::user()->name);
                $printInfoLine("Member", $penjualan->member->nama ?? '-');
                $printer->text("-------------------------------\n");

                // Cetak daftar barang yang dibeli
                $totalQty = 0;
                $totalHarga = 0;

                foreach ($items as $item) {
                    $produk = Produk::find($item['produk_id']);
                    $namaProduk = $produk->nama_barang ?? 'Produk';
                    $qty = $item['jumlah'];
                    $hargaSatuan = $item['harga_jual'];
                    $subtotal = $qty * $hargaSatuan;
                    $totalQty += $qty;
                    $totalHarga += $subtotal;

                    // Cetak nama produk
                    $printer->text(substr($namaProduk, 0, 24) . "\n");

                    // Cetak qty dan harga produk
                    $priceLine = str_pad($qty . " X", 5, ' ', STR_PAD_LEFT)
                        . str_pad(number_format($hargaSatuan, 0, ',', '.'), 10, ' ', STR_PAD_LEFT)
                        . str_pad(number_format($subtotal, 0, ',', '.'), 12, ' ', STR_PAD_LEFT);
                    $printer->text($priceLine . "\n\n");
                }

                $printer->text("-------------------------------\n");

                // Cetak ringkasan pembayaran
                $printPaymentLine = function ($label, $value) use ($printer) {
                    $line = str_pad($label . ":", 14) . str_pad($value, 18, ' ', STR_PAD_LEFT);
                    $printer->text($line . "\n");
                };

                $printPaymentLine("Total Bayar", "Rp " . number_format($totalHarga, 0, ',', '.'));
                $printPaymentLine("Total Qty", $totalQty);
                $printPaymentLine("Bayar", "Rp " . number_format($request->uang_pelanggan ?? 0, 0, ',', '.'));
                $printPaymentLine("Kembalian", "Rp " . number_format(($request->uang_pelanggan ?? 0) - $totalHarga, 0, ',', '.'));

                $printer->text("==============================\n");

                // Footer struk
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("\nTerima kasih telah\n");
                $printer->text("berbelanja di Bian Mart!\n");
                $printer->text("==============================\n");

                $printer->pulse();
                $printer->cut();
                $printer->close();
            } catch (\Exception $ex) {
                Log::error("Gagal mencetak struk: " . $ex->getMessage());
            }

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
            DB::rollBack(); // Jika terjadi error, batalkan transaksi
            return response()->json([
                'message' => 'Terjadi kesalahan!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
