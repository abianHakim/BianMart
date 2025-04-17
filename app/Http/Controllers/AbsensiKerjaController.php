<?php

namespace App\Http\Controllers;

use App\Exports\AbsensiFormatExport;
use App\Exports\AbsensiKerjaExport;
use App\Imports\AbsensiKerjaImport;
use App\Models\AbsenKerja;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;


/**
 * Enum untuk status absensi. Nilai nya adalah string.
 * @package App\Http\Controllers
 * @method static AbsensiStatus MASUK()
 * @method static AbsensiStatus SAKIT()
 * @method static AbsensiStatus CUTI()
 */
enum AbsensiStatus: string
{
    case MASUK = 'masuk';
    case SAKIT = 'sakit';
    case CUTI = 'cuti';
}

/**
 * Controller untuk mengelola data absensi.
 * @package App\Http\Controllers
 */
class AbsensiKerjaController extends Controller
{
    /**
     * Tampilkan halaman index absensi.
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Mengambil data absensi bersama data user (karyawan)
        $absensi = AbsenKerja::with('user')->get();  // Mengambil relasi user juga

        return view('admin.absensi.absen', compact('absensi'));
    }


    /**
     * Simpan data absensi baru.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_masuk' => 'required|date',
            'waktu_masuk' => 'required|date_format:H:i:s',
            'status_masuk' => ['required', Rule::in(array_column(AbsensiStatus::cases(), 'value'))],
        ]);

        // Set waktu_selesai_kerja menjadi null jika status masuk adalah 'masuk', jika tidak set ke '00:00:00'
        $waktu_selesai_kerja = ($request->status_masuk == AbsensiStatus::MASUK->value) ? null : '00:00:00';

        // Simpan data absensi
        AbsenKerja::create([
            'user_id' => $request->user_id, // Ganti nama_karyawan dengan user_id
            'tanggal_masuk' => $request->tanggal_masuk,
            'waktu_masuk' => $request->waktu_masuk,
            'status_masuk' => $request->status_masuk,
            'waktu_selesai_kerja' => $waktu_selesai_kerja,
        ]);

        return redirect()->back()->with('success', 'Data absensi berhasil ditambahkan.');
    }



    /**
     * Perbarui data absensi yang sudah ada.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'status_masuk' => 'required|in:masuk,sakit,cuti',
            'user_id' => 'required|exists:users,id',
        ]);

        // Cari data absensi berdasarkan ID
        $absen = AbsenKerja::findOrFail($id);

        // Update status_masuk, user_id dan waktu_selesai_kerja
        $absen->update([
            'status_masuk' => $request->status_masuk,
            'user_id' => $request->user_id,  // Menambahkan pembaruan user_id
            'waktu_selesai_kerja' => ($request->status_masuk == 'masuk') ? $absen->waktu_selesai_kerja : '00:00:00',
        ]);

        return redirect()->back()->with('success', 'Data absensi berhasil diperbarui.');
    }

    public function updateStatus(Request $request, $id)
    {
        // Validasi status
        $request->validate([
            'status_masuk' => 'required|in:masuk,sakit,cuti',
        ]);

        // Cari data absensi berdasarkan ID
        $absen = AbsenKerja::findOrFail($id);

        // Update status_masuk dan waktu_selesai_kerja
        $absen->update([
            'status_masuk' => $request->status_masuk,
            'waktu_selesai_kerja' => ($request->status_masuk == 'masuk') ? $absen->waktu_selesai_kerja : '00:00:00',
        ]);

        return redirect()->back()->with('success', 'Status absensi berhasil diperbarui.');
    }



    /**
     * Hapus data absensi.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // dd($id);
        AbsenKerja::destroy($id);
        return redirect()->back()->with('success', 'Data berhasil dihapus.');
    }

    /**
     * Ubah status absensi menjadi SELESAI.
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function selesaiKerja($id)
    {
        // dd($id);
        $absen = AbsenKerja::findOrFail($id);
        $absen->update([
            'waktu_selesai_kerja' => now()->format('H:i:s')
        ]);
        return redirect()->back()->with('success', 'Data absensi berhasil diperbarui.');
    }


    /**
     * Ekspor data absensi ke file Excel.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel()
    {
        return Excel::download(new AbsensiKerjaExport, 'absensi-kerja.xlsx');
    }

    /**
     * Ekspor data absensi ke file PDF.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportPDF()
    {
        $data = AbsenKerja::all();
        $pdf = Pdf::loadView('admin.absensi.absensi_export_pdf', compact('data'));
        return $pdf->download('absensi-kerja.pdf');
    }

    /**
     * Impor data absensi dari file Excel.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
        // Proses import file
        Excel::import(new AbsensiKerjaImport, $request->file('file'));
        // Berikan feedback ke pengguna
        return redirect()->back()->with('success', 'Data absensi berhasil di-import.');
    }

    /**
     * Unduh template import absensi.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadFormat()
    {
        return Excel::download(new AbsensiFormatExport, 'template import absensi.xlsx');
    }
}
