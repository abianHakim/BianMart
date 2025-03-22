<?php

namespace App\Http\Controllers;

use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminHomeController extends Controller
{
    public function index()
    {
        $jumlah_produk_tersedia = StokBarang::sum('stok_toko');
        $total_transaksi = Penjualan::whereDate('tgl_faktur', today())->count();
        $pendapatan_hari_ini = Penjualan::whereDate('tgl_faktur', today())->sum('total_bayar');
        $produk_hampir_habis = StokBarang::where('stok_toko', '<', 10)->count();

        // Data Penjualan Bulanan
        $penjualan_bulanan = Penjualan::select(DB::raw('MONTH(tgl_faktur) as bulan'), DB::raw('SUM(total_bayar) as total'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $bulan = $penjualan_bulanan->pluck('bulan')->map(function ($b) {
            return date('F', mktime(0, 0, 0, $b, 1));
        })->toArray();
        $total_penjualan = $penjualan_bulanan->pluck('total')->toArray();

        // Produk Terlaris
        $produk_terlaris = DetailPenjualan::select('produk.nama_barang', DB::raw('SUM(jumlah) as jumlah_terjual'))
            ->join('produk', 'detail_penjualan.produk_id', '=', 'produk.id')
            ->groupBy('produk.nama_barang')
            ->orderByDesc('jumlah_terjual')
            ->limit(5)
            ->get();

        $nama_produk = $produk_terlaris->pluck('nama_barang');
        $jumlah_terjual = $produk_terlaris->pluck('jumlah_terjual');

        return view('admin.home.admin', compact(
            'jumlah_produk_tersedia',
            'total_transaksi',
            'pendapatan_hari_ini',
            'produk_hampir_habis',
            'bulan',
            'total_penjualan',
            'nama_produk',
            'jumlah_terjual'
        ));
    }


    public function liveTransactions()
    {
        $transactions = Penjualan::select(
            DB::raw('DATE(tgl_faktur) as tanggal'),
            DB::raw('COUNT(id) as jumlah_transaksi'),
            DB::raw('SUM(total_bayar) as total_pendapatan')
        )
            ->where('tgl_faktur', '>=', Carbon::now()->subDays(7))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        return response()->json($transactions);
    }
}
