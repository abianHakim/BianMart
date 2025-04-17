<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPenjualanExport;
use App\Exports\PembelianExport;
use App\Exports\PenjualanExport;
use App\Models\PenerimaanBarang;
use App\Models\Transaksi;

use App\Models\Penjualan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    /**
     * Menampilkan laporan penjualan berdasarkan filter tanggal.
     *
     * Fungsi ini mengembalikan data penjualan yang terkait dengan tanggal yang diberikan.
     * Menampilkan laporan pada halaman admin dengan filter tanggal awal dan akhir.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function penjualan(Request $request)
    {
        $tanggal_awal = $request->tanggal_awal;
        $tanggal_akhir = $request->tanggal_akhir;

        $penjualans = Penjualan::with(['user', 'member'])
            ->when($tanggal_awal && $tanggal_akhir, function ($query) use ($tanggal_awal, $tanggal_akhir) {
                $query->whereBetween('tgl_faktur', [$tanggal_awal, $tanggal_akhir]);
            })
            ->latest()
            ->get();

        return view('admin.laporan.penjualan', compact('penjualans', 'tanggal_awal', 'tanggal_akhir'));
    }

    /**
     * Mengekspor laporan penjualan ke dalam format PDF berdasarkan filter tanggal.
     *
     * Fungsi ini mengambil data penjualan sesuai dengan rentang tanggal yang diberikan oleh pengguna
     * dan mengonversinya menjadi file PDF yang dapat diunduh.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPenjualanPDF(Request $request)
    {
        $tanggal_awal = $request->tanggal_awal;
        $tanggal_akhir = $request->tanggal_akhir;

        $penjualans = Penjualan::with(['user', 'member'])
            ->when($tanggal_awal && $tanggal_akhir, function ($query) use ($tanggal_awal, $tanggal_akhir) {
                $start = Carbon::parse($tanggal_awal)->startOfDay();
                $end = Carbon::parse($tanggal_akhir)->endOfDay();
                $query->whereBetween('tgl_faktur', [$start, $end]);
            })
            ->get();

        $pdf = PDF::loadView('admin.laporan.export.Penjaualan_export_pdf', compact('penjualans'));

        return $pdf->download('laporan_penjualan.pdf');
    }

    /**
     * Mengekspor laporan penjualan ke dalam format Excel berdasarkan filter tanggal.
     *
     * Fungsi ini menggunakan `LaporanPenjualanExport` untuk menghasilkan file Excel
     * yang berisi data penjualan sesuai dengan rentang tanggal yang diberikan.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPenjualanExcel(Request $request)
    {
        $start = $request->tanggal_awal;
        $end = $request->tanggal_akhir;

        return Excel::download(new LaporanPenjualanExport($start, $end), 'laporan_penjualan.xlsx');
    }

    /**
     * Menampilkan laporan pembelian berdasarkan filter tanggal.
     *
     * Fungsi ini menampilkan data pembelian dari penerimaan barang sesuai dengan rentang
     * tanggal yang diberikan oleh pengguna, serta menampilkan hasilnya dalam laporan.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function pembelian(Request $request)
    {
        $query = PenerimaanBarang::with('supplier', 'detailPenerimaan.produk');

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $tanggalAwal = Carbon::parse($request->tanggal_awal)->startOfDay();
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();

            $query->whereBetween('tgl_masuk', [$tanggalAwal, $tanggalAkhir]);
        }

        $laporanPembelian = $query->get();

        return view('admin.laporan.pembelian', compact('laporanPembelian'));
    }

    /**
     * Mengekspor laporan pembelian ke dalam format PDF berdasarkan filter tanggal.
     *
     * Fungsi ini mengambil data pembelian sesuai dengan rentang tanggal yang diberikan oleh pengguna
     * dan mengonversinya menjadi file PDF yang dapat diunduh.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPembelianPdf(Request $request)
    {
        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;

        $laporanPembelian = PenerimaanBarang::with(['supplier', 'detailPenerimaan.produk'])
            ->when($tanggalAwal && $tanggalAkhir, function ($query) use ($tanggalAwal, $tanggalAkhir) {
                $query->whereBetween('tgl_masuk', [$tanggalAwal, $tanggalAkhir]);
            })
            ->get();

        $pdf = PDF::loadView('admin.laporan.export.Pembelian_export_pdf', compact('laporanPembelian', 'tanggalAwal', 'tanggalAkhir'));
        return $pdf->download('laporan_pembelian.pdf');
    }

    /**
     * Mengekspor laporan pembelian ke dalam format Excel berdasarkan filter tanggal.
     *
     * Fungsi ini menggunakan `PembelianExport` untuk menghasilkan file Excel
     * yang berisi data pembelian sesuai dengan rentang tanggal yang diberikan.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportPembelianExcel(Request $request)
    {
        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;

        return Excel::download(new PembelianExport($tanggalAwal, $tanggalAkhir), 'laporan_pembelian.xlsx');
    }
}
