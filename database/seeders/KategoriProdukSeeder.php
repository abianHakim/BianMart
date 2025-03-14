<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class KategoriProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // KategoriProduk::factory()->count(6)->create();

        Schema::disableForeignKeyConstraints();
        KategoriProduk::truncate();
        Schema::enableForeignKeyConstraints();
        $file = File::get('database/data/seder_kategori.json');
        $data = json_decode($file);

        foreach ($data as $value) {
            KategoriProduk::create([
                // 'id' => $value->id,
                'nama_kategori'=> $value->nama_kategori,

            ]);
        }
    }
}
