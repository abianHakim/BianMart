<?php

namespace Tests\Feature;

use App\Models\KategoriProduk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class kategoriControllerTest extends TestCase
{
    // Jangan pakai RefreshDatabase kalau tidak ingin hapus user aslinya
    // use RefreshDatabase;

    public function testStoreSuccessfully(): void
    {
        // Ambil user yang sudah ada (pastikan user ini memang ada di database)
        $user = User::where('email', 'abian@gmail.com')->first();
        // Pastikan user ditemukan
        $this->assertNotNull($user, 'User abian@gmail.com tidak ditemukan di database');
        // Login sebagai user tersebut
        $this->actingAs($user);

        $data = [
            'nama_kategori' => 'sabun',
        ];

        // Kirim POST ke /kategori
        $response = $this->post('/kategori', $data);

        // Pastikan redirect berhasil
        $response->assertStatus(302);
        $response->assertRedirect(route('kategori.index'));

        // Cek data masuk ke database
        $this->assertDatabaseHas('kategori_produk', $data);
    }

    public function testUpdateSuccessfully(): void
    {
        $user = User::where('email', 'abian@gmail.com')->first();
        $this->assertNotNull($user, 'User abian@gmail.com tidak ditemukan di database');
        $this->actingAs($user);

        // Ambil kategori yang sudah ada di database (pastikan datanya memang ada di seeder atau hasil testing sebelumnya)
        $kategori = KategoriProduk::where('nama_kategori', 'sabun')->first();
        $this->assertNotNull($kategori, 'Kategori dengan nama "sabun" tidak ditemukan di database');

        //OR

        // Simpan kategori dulu untuk di-update
        // $kategori = KategoriProduk::create([
        //     'nama_kategori' => 'Kategori Lama'
        // ]);

        // Data yang akan diupdate
        $updateData = [
            'nama_kategori' => 'sabun luxs'
        ];

        // Lakukan request PATCH/PUT untuk update
        $response = $this->patch('/kategori/' . $kategori->id, $updateData);
        $response->assertStatus(302);
        $response->assertRedirect(route('kategori.index'));

        $this->assertDatabaseHas('kategori_produk', $updateData);
    }

    public function testDeleteSuccessfully(): void
    {
        // Ambil user yang sudah ada
        $user = User::where('email', 'abian@gmail.com')->first();
        $this->assertNotNull($user, 'User abian@gmail.com tidak ditemukan di database');
        $this->actingAs($user);

        // Ambil data kategori yang sudah ada
        $kategori = KategoriProduk::where('nama_kategori', 'sabun luxs')->first();
        $this->assertNotNull($kategori, 'Kategori "sabun luxs" tidak ditemukan di database');

        //OR

        // Simpan kategori dulu untuk di-update
        // $kategori = KategoriProduk::create([
        //     'nama_kategori' => 'Kategori Lama'
        // ]);

        // Lakukan request DELETE ke endpoint kategori
        $response = $this->delete('/kategori/' . $kategori->id);
        $response->assertStatus(302);
        $response->assertRedirect(route('kategori.index'));

        // Pastikan data sudah terhapus dari database
        $this->assertDatabaseMissing('kategori_produk', [
            'id' => $kategori->id,
            'nama_kategori' => 'sabun luxs'
        ]);
    }
}
