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
    /**
     * Menampilkan daftar pengajuan milik member yang sedang login.
     *
     * Fungsi ini menampilkan daftar pengajuan barang yang dibuat oleh member yang sedang login
     * berdasarkan ID member yang diambil dari session. Data pengajuan disertai dengan informasi
     * member yang mengajukan.
     *
     * @return \Illuminate\View\View Tampilan halaman pengajuan untuk member
     */
    public function index()
    {
        $memberId = Auth::guard('member')->id();

        $pengajuan = Pengajuan::with('member')
            ->where('member_id', $memberId)
            ->get();

        return view('member.pengajuan.pengajuan', compact('pengajuan'));
    }

    /**
     * Menampilkan semua pengajuan (untuk admin).
     *
     * Fungsi ini menampilkan daftar pengajuan barang yang ada, tanpa memfilter berdasarkan
     * member. Hal ini ditujukan untuk admin yang ingin melihat seluruh pengajuan yang telah
     * dibuat.
     *
     * @return \Illuminate\View\View Tampilan halaman daftar pengajuan untuk admin
     */
    public function pengajuanAll()
    {
        $pengajuan = Pengajuan::all();
        return view('admin.Daftar_pengajuan.pengajuanAll', compact('pengajuan'));
    }

    /**
     * Menyimpan pengajuan baru.
     *
     * Fungsi ini menyimpan pengajuan barang baru yang dibuat oleh member. Data pengajuan
     * akan disimpan di database dengan ID member yang sedang login, nama barang, kuantitas,
     * dan status terpenuhi yang diset ke 0. Log aktivitas juga akan disimpan.
     *
     * @param \Illuminate\Http\Request $request Data yang diterima dari form pengajuan
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman sebelumnya dengan pesan sukses
     */
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

    /**
     * Memperbarui data pengajuan (hanya bisa dilakukan jika belum terpenuhi).
     *
     * Fungsi ini memperbarui pengajuan yang telah dibuat oleh member, hanya jika status
     * pengajuan tersebut masih belum terpenuhi. Setelah data pengajuan diperbarui, log
     * aktivitas juga akan disimpan.
     *
     * @param \Illuminate\Http\Request $request Data yang diterima dari form pengajuan
     * @param int $id ID pengajuan yang akan diperbarui
     * @return \Illuminate\Http\RedirectResponse Redirect ke halaman sebelumnya dengan pesan sukses
     */
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

    /**
     * Memperbarui status terpenuhi pengajuan barang.
     *
     * Fungsi ini digunakan oleh admin untuk mengubah status terpenuhi atau tidaknya pengajuan
     * barang yang ada. Status pengajuan akan disimpan ke database, dan aktivitas ini juga
     * akan dicatat dalam log.
     *
     * @param \Illuminate\Http\Request $request Data yang diterima dari form status pengajuan
     * @param int $id ID pengajuan yang statusnya akan diperbarui
     * @return \Illuminate\Http\JsonResponse Respon JSON dengan pesan sukses
     */
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

    /**
     * Menghapus pengajuan barang berdasarkan ID.
     *
     * Fungsi ini menghapus pengajuan barang yang dibuat oleh member, hanya jika status pengajuan
     * masih belum terpenuhi. Setelah pengajuan dihapus, log aktivitas akan disimpan.
     *
     * @param int $id ID pengajuan yang akan dihapus
     * @return \Illuminate\Http\JsonResponse Respon JSON dengan pesan sukses
     */
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

    /**
     * Mengekspor data pengajuan ke format PDF.
     *
     * Fungsi ini mengekspor data pengajuan berdasarkan filter tanggal yang diberikan oleh admin
     * ke dalam file PDF. Setelah proses ekspor selesai, file PDF akan diunduh oleh pengguna.
     *
     * @param \Illuminate\Http\Request $request Data yang diterima dari form filter tanggal
     * @return \Illuminate\Http\Response Respon berupa unduhan file PDF
     */
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

    /**
     * Mengekspor data pengajuan ke format Excel.
     *
     * Fungsi ini mengekspor data pengajuan ke dalam format Excel dengan menggunakan Laravel Excel.
     * File Excel akan diunduh setelah proses ekspor selesai.
     *
     * @param \Illuminate\Http\Request $request Data yang diterima dari form filter
     * @return \Illuminate\Http\Response Respon berupa unduhan file Excel
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(new PengajuanExport(), 'pengajuan.xlsx');
    }
}
