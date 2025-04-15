<?php

namespace App\Exports;

use App\Models\Penjualan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class LaporanPenjualanExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths
{
    protected $start, $end;

    // Konstruktor untuk menerima tanggal mulai dan tanggal akhir
    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    // Ambil data dari database
    public function collection()
    {
        $query = Penjualan::query();

        // Terapkan filter tanggal jika ada
        if ($this->start && $this->end) {
            $query->whereBetween('tgl_faktur', [$this->start, $this->end]);
        }

        // Ambil data penjualan sesuai rentang tanggal
        return $query->with('user', 'member')->get();
    }

    // Menentukan Heading (Header) di file Excel
    public function headings(): array
    {
        return [
            'No Faktur',
            'Tanggal',
            'Kasir',
            'Member',
            'Total Bayar',
            'Metode Pembayaran',
        ];
    }

    // Menentukan cara pemetaan data dari query ke Excel
    public function map($penjualan): array
    {
        return [
            $penjualan->no_faktur,
            $penjualan->tgl_faktur,
            $penjualan->user->name ?? 'N/A',
            $penjualan->member->nama ?? 'N/A',
            'Rp ' . number_format($penjualan->total_bayar, 0, ',', '.'),
            ucfirst($penjualan->metode_pembayaran),
        ];
    }

    // Menentukan lebar kolom di file Excel
    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 20,
            'C' => 30,
            'D' => 30,
            'E' => 20,
            'F' => 25,
        ];
    }
}
