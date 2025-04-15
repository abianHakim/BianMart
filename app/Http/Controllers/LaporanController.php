<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPenjualanExport;
use App\Exports\PenjualanExport;
use App\Models\Transaksi;

use App\Models\Penjualan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
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

    public function exportPDF(Request $request)
    {
        // Mendapatkan tanggal_awal dan tanggal_akhir dari request
        $tanggal_awal = $request->tanggal_awal;
        $tanggal_akhir = $request->tanggal_akhir;

        // Mengambil data penjualan dengan filter tanggal
        $penjualans = Penjualan::with(['user', 'member'])
            ->when($tanggal_awal && $tanggal_akhir, function ($query) use ($tanggal_awal, $tanggal_akhir) {
                $start = Carbon::parse($tanggal_awal)->startOfDay();
                $end = Carbon::parse($tanggal_akhir)->endOfDay();
                $query->whereBetween('tgl_faktur', [$start, $end]);
            })
            ->get();

        // Mengenerate PDF dengan data yang sudah difilter
        $pdf = PDF::loadView('admin.laporan.export.Penjaualan_export_pdf', compact('penjualans'));

        // Mengunduh file PDF
        return $pdf->download('laporan_penjualan.pdf');
    }

    public function exportExcel(Request $request)
    {
        // Mendapatkan tanggal_awal dan tanggal_akhir dari request
        $start = $request->tanggal_awal;
        $end = $request->tanggal_akhir;

        // Menggunakan LaporanPenjualanExport untuk ekspor Excel dengan filter tanggal
        return Excel::download(new LaporanPenjualanExport($start, $end), 'laporan_penjualan.xlsx');
    }
}
