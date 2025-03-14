<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produk>
 */
class ProdukFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $data = DB::table('kategori_produk')->inRandomOrder()->select('id')->first();
        $supplier = DB::table('supplier')->inRandomOrder()->select('id')->first();

        return [
            'kode_barang' => fake()->unique()->numberBetween(100000, 999999),
            'nama_barang' => fake()->unique()->randomElement([
                'Sabun Bubuk',
                'Sabun Cair',
                'Obat Sakit Kepala',
                'Obat Sakit Perut',
                'Parfume Wanita',
                'Parfume Pria',
                'Kebutuhan Bayi',
                'Bumbu Makanan',
                'Ice Cream',
                'Kopi Hitam',
                'Kopi Putih',
                'Teh Manis',
                'Teh Tawar',
                'Gula Pasir',
                'Gula Batu',
                'Kopi Instan',
                'Susu Bubuk',
                'Susu Cair',
                'Telur Ayam',
                'Telur Bebek'
            ]),
            'kategori_id' => $data->id,
            'supplier_id' => $supplier->id,
            'persentase_keuntungan' => fake()->numberBetween(1, 99),
            'harga_beli' => fake()->numberBetween(100000, 999999),
            'deskripsi' => fake()->paragraph(2),
            'satuan' => fake()->randomElement(['pcs', 'gram', 'liter', 'ml', 'kg', 'ton']),

        ];
    }
}
