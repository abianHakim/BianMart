<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AbsensiFormatExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['Contoh', '2025-04-16', '08.00.00', 'Masuk', '00.00.00'],
            ['Contoh 2', '2025-04-16', '09.00.00', 'Cuti', '00.00.00'],
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Karyawan',
            'Tanggal Masuk',
            'Waktu Masuk',
            'Status Masuk',
            'Waktu Selesai Kerja',
        ];
    }
}
