<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('member')->insert([
            [
                'nama' => 'Dita Ismi',
                'email' => 'john.doe@example.com',
                'password' => Hash::make('12345678'),
                'no_telp' => '1234567890',
                'alamat' => 'Jl. gunteng No. 123',
                'tgl_bergabung' => now(),
            ],
            [
                'nama' => 'Fathan Fasya',
                'email' => 'jane.smith@example.com',
                'password' => Hash::make('12345678'),
                'no_telp' => '123456789',
                'alamat' => 'Jl. salakopi No. 456',
                'tgl_bergabung' => now(),
            ],
        ]);
    }
}
