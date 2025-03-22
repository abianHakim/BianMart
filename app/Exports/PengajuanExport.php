<?php

namespace App\Exports;

use App\Models\Pengajuan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PengajuanExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithMapping
{
    protected $dataCount;

    public function collection()
    {
        $pengajuan = Pengajuan::select('id', 'nama_barang', 'tanggal_pengajuan', 'qty', 'terpenuhi')->get();
        $this->dataCount = $pengajuan->count(); // Simpan jumlah data
        return $pengajuan;
    }

    // Judul Header
    public function headings(): array
    {
        return [
            'ID',
            'Nama Barang',
            'Tanggal Pengajuan',
            'Jumlah',
            'Status'
        ];
    }

    // Konversi Status dari 0/1 ke "Terpenuhi" / "Tidak"
    public function map($pengajuan): array
    {
        return [
            $pengajuan->id,
            $pengajuan->nama_barang,
            $pengajuan->tanggal_pengajuan,
            $pengajuan->qty,
            $pengajuan->terpenuhi ? 'Terpenuhi' : 'Tidak' // Konversi status
        ];
    }

    // Styling
    public function styles(Worksheet $sheet)
    {
        // Tentukan batas akhir data secara dinamis
        $lastRow = $this->dataCount + 1; // Header di baris 1, data mulai dari 2

        return [
            // Header warna biru muda dan teks bold
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4A86E8']],
                'alignment' => ['horizontal' => 'center']
            ],
            // Border hanya sampai baris terakhir data
            "A2:E$lastRow" => [
                'alignment' => ['horizontal' => 'center'],
                'borders' => ['allBorders' => ['borderStyle' => 'thin']]
            ],
        ];
    }

    // Atur Lebar Kolom
    public function columnWidths(): array
    {
        return [
            'A' => 10,  // ID
            'B' => 30,  // Nama Barang
            'C' => 20,  // Tanggal Pengajuan
            'D' => 15,  // Jumlah
            'E' => 15,  // Status
        ];
    }
}
