<?php

namespace App\Imports;

use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProdukImport implements ToModel, WithHeadingRow
{

    public function model(array $row)
    {
        // dd($row);
        // Cek apakah semua data tersedia
        if (!isset($row['kategori']) || !isset($row['supplier'])) {
            throw new \Exception('Kolom kategori atau supplier tidak ditemukan.');
        }

        // Ambil ID kategori dari nama
        $kategori = KategoriProduk::where('nama_kategori', $row['kategori'])->first();
        if (!$kategori) {
            throw new \Exception('Kategori "' . $row['kategori'] . '" tidak ditemukan di database.');
        }

        // Ambil ID supplier dari nama
        $supplier = Supplier::where('nama_supplier', $row['supplier'])->first();
        if (!$supplier) {
            throw new \Exception('Supplier "' . $row['supplier'] . '" tidak ditemukan di database.');
        }

        return new Produk([
            'kode_barang' => $row['kode_barang'],
            'nama_barang' => $row['nama_barang'],
            'kategori_id' => $kategori->id,
            'supplier_id' => $supplier->id,
            'harga_beli' => $row['harga_beli'],
            'persentase_keuntungan' => $row['persentase_keuntungan'],
            'deskripsi' => $row['deskripsi'],
            'satuan' => 'pcs',
        ]);
    }
}
