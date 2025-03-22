<?php

namespace App\Http\Controllers; // ✅ Mengelompokkan class ke dalam namespace tertentu


use App\Exports\PengajuanExport;
use App\Helpers\LogHelper;
use App\Models\Pengajuan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PengajuanController extends Controller
{
    // Menampilkan daftar pengajuan milik member yang sedang login
    public function index()
    {
        $memberId = Auth::guard('member')->id();

        $pengajuan = Pengajuan::with('member')
            ->where('member_id', $memberId)
            ->get();

        return view('member.pengajuan.pengajuan', compact('pengajuan'));
    }

    // Menampilkan semua pengajuan (untuk admin)
    public function pengajuanAll()
    {
        $pengajuan = Pengajuan::all();
        return view('admin.Daftar_pengajuan.pengajuanAll', compact('pengajuan'));
    }

    // Menyimpan pengajuan baru
    public function store(Request $request)
    {
        $memberId = Auth::guard('member')->id();

        // Validasi data
        $validatedData = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
        ]);

        // Simpan pengajuan
        $pengajuan = Pengajuan::create([
            'member_id' => $memberId,
            'nama_barang' => $request->nama_barang,
            'tanggal_pengajuan' => now(),
            'qty' => $request->qty,
            'terpenuhi' => 0,
        ]);

        // Simpan ke log
        LogHelper::create('Pengajuan Barang', "Member ID $memberId mengajukan barang: {$request->nama_barang} ({$request->qty} pcs).");


        return redirect()->back()->with('success', 'Pengajuan berhasil dibuat!');
    }

    // Memperbarui data pengajuan (hanya bisa dilakukan jika belum terpenuhi)
    public function update(Request $request, $id)
    {
        $memberId = Auth::guard('member')->id();

        // Validasi data
        $validatedData = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
        ]);

        // Ambil pengajuan yang belum terpenuhi
        $pengajuan = Pengajuan::where('id', $id)
            ->where('member_id', $memberId)
            ->where('terpenuhi', 0)
            ->firstOrFail();

        // Update data
        $pengajuan->update([
            'nama_barang' => $request->nama_barang,
            'qty' => $request->qty,
        ]);

        // Simpan ke log
        LogHelper::create('Perubahan Pengajuan', "Member ID $memberId mengubah pengajuan ID $id menjadi: {$request->nama_barang} ({$request->qty} pcs).");


        return redirect()->back()->with('success', 'Pengajuan berhasil diperbarui!');
    }

    public function updateStatus(Request $request, $id)
    {
        // Validasi input
        $validatedData = $request->validate([
            'terpenuhi' => 'required|boolean',
        ]);

        $pengajuan = Pengajuan::findOrFail($id);
        $pengajuan->terpenuhi = $request->terpenuhi;
        $pengajuan->save();

        // Simpan ke log
        LogHelper::create('Perubahan Status Pengajuan', "Admin ID " . Auth::id() . " mengubah status pengajuan ID $id menjadi " . ($request->terpenuhi ? "Terpenuhi" : "Belum Terpenuhi") . ".");


        return response()->json(['message' => 'Status pengajuan berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $memberId = Auth::guard('member')->id();

        $pengajuan = Pengajuan::where('id', $id)
            ->where('member_id', $memberId)
            ->where('terpenuhi', 0)
            ->firstOrFail();

        $pengajuan->delete();

        // Simpan ke log
        LogHelper::create('Penghapusan Pengajuan', "Member ID $memberId menghapus pengajuan ID $id.");


        return response()->json(['message' => 'Pengajuan berhasil dihapus!']);
    }

    public function exportPDF(Request $request)
    {
        $query = Pengajuan::query();

        // Ambil tanggal dari request
        if ($request->has('start') && $request->has('end')) {
            $start = $request->start;
            $end = $request->end;

            if (!empty($start) && !empty($end)) {
                $query->whereBetween('tanggal_pengajuan', [$start, $end]);
            }
        }

        // Ambil data sesuai filter
        $pengajuan = $query->get();

        $pdf = Pdf::loadView('admin.Daftar_pengajuan.export_pdf', compact('pengajuan'));

        return $pdf->download('daftar_pengajuan.pdf');
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->query('start');
        $endDate = $request->query('end');

        return Excel::download(new PengajuanExport($startDate, $endDate), 'pengajuan.xlsx');
    }
}
