<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;



/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KategoriProduk>
 */
class KategoriProdukFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_kategori' => fake()->unique()->randomElement(['Obat', 'Sabun', 'Bumbu', 'Parfume', 'Kebutuhan Bayi', 'Ice Cream']),
        ];
    }
}
