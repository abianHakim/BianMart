<?php

namespace App\Exports;

use App\Models\AbsenKerja;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AbsensiKerjaExport implements FromCollection, WithHeadings, WithColumnFormatting, WithStyles
{
    /**
     * Mengambil data untuk diexport
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return AbsenKerja::all()->map(function ($item) {
            // Keterangan berdasarkan status
            $keterangan = match ($item->status_masuk) {
                'masuk' => 'Bekerja',
                'cuti' => 'Cuti',
                'sakit' => 'Sakit',
                default => '',
            };

            return [
                $item->id,
                $item->user->name,
                ucfirst($item->status_masuk),
                $item->tanggal_masuk ? \Carbon\Carbon::parse($item->tanggal_masuk)->format('Y-m-d') : null,
                $item->waktu_masuk ? \Carbon\Carbon::parse($item->waktu_masuk)->format('H:i:s') : null,
                $item->waktu_selesai_kerja ? \Carbon\Carbon::parse($item->waktu_selesai_kerja)->format('H:i:s') : null,
                $keterangan,
            ];
        });
    }



    /**
     * Menentukan judul kolom untuk file Excel
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nama User',
            'Status Masuk',
            'Tanggal Masuk',
            'Waktu Masuk',
            'Waktu Selesai Kerja',
            'Keterangan'
        ];
    }


    /**
     * Menentukan format kolom yang perlu diatur
     *
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'D' => 'yyyy-mm-dd',
            'E' => 'hh:mm:ss',
            'F' => 'hh:mm:ss',
            'G' => 'yyyy-mm-dd hh:mm:ss',
        ];
    }


    /**
     * Mengatur gaya dan lebar kolom di Excel
     *
     * @param Worksheet $sheet
     * @return void
     */
    public function styles(Worksheet $sheet)
    {
        // Atur auto-fit untuk kolom
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);

        // Mengatur gaya header
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A1:G1')->getAlignment()->setVertical('center');
    }
}
