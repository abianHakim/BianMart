<?php

namespace App\Exports;

use App\Models\PenerimaanBarang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PembelianExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tanggalAwal, $tanggalAkhir;

    public function __construct($tanggalAwal, $tanggalAkhir)
    {
        $this->tanggalAwal = $tanggalAwal;
        $this->tanggalAkhir = $tanggalAkhir;
    }

    public function collection()
    {
        return PenerimaanBarang::with(['supplier', 'detailPenerimaan.produk'])
            ->when($this->tanggalAwal && $this->tanggalAkhir, function ($query) {
                $query->whereBetween('tgl_masuk', [$this->tanggalAwal, $this->tanggalAkhir]);
            })
            ->get();
    }

    public function map($penerimaan): array
    {
        $rows = [];
        foreach ($penerimaan->detailPenerimaan as $detail) {
            $rows[] = [
                \Carbon\Carbon::parse($penerimaan->tgl_masuk)->format('d-m-Y'),
                $penerimaan->supplier->nama_supplier ?? '-',
                $detail->produk->nama_barang ?? '-',
                $detail->jumlah,
                'Rp ' . number_format($detail->harga_beli, 0, ',', '.'),
                'Rp ' . number_format($detail->jumlah * $detail->harga_beli, 0, ',', '.')
            ];
        }
        return $rows;
    }

    public function headings(): array
    {
        return ['Tanggal', 'Supplier', 'Nama Produk', 'Jumlah', 'Harga Beli', 'Total'];
    }
}
