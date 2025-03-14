<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class SupplierSeeder extends Seeder
{


    public function run(): void
    {
        // Schema::disableForeignKeyConstraints();
        // Supplier::truncate();
        // Schema::enableForeignKeyConstraints();
        // $file = File::get('');
        // $data = json_decode($file);

        // foreach ($data as $value) {
        //     Supplier::create([
        //         'id' => $value->id,

        //     ]);
        // }

        Supplier::factory()->count(10)->create();

    }
}
