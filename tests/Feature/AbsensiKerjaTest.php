<?php

namespace Tests\Feature;

use App\Models\AbsenKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AbsensiKerjaTest extends TestCase
{

    public function test_tambah_absensi_berhasil(): void
    {
        $user = User::where('email', 'abian@gmail.com')->first();
        $this->assertNotNull($user, 'User abian@gmail.com tidak ditemukan di database');
        $this->actingAs($user);

        $data = [
            'nama_karyawan' => 'Ujang',
            'tanggal_masuk' => '2025-04-17',
            'waktu_masuk' => '08:00:00',
            'status_masuk' => 'masuk',
        ];

        $response = $this->post(route('absensi.store'), $data);
        $response->assertStatus(302);

        $this->assertDatabaseHas('tbl_absen_kerja', $data);
    }

    public function test_tambah_absensi_gagal(): void
    {
        $user = User::where('email', 'abian@gmail.com')->first();
        $this->assertNotNull($user, 'User abian@gmail.com tidak ditemukan di database');
        $this->actingAs($user);

        $data = [
            'nama_karyawan' => '',
            'tanggal_masuk' => '',
            'waktu_masuk' => '',
            'status_masuk' => '',
        ];

        $response = $this->post(route('absensi.store'), $data);
        $response->assertSessionHasErrors(['nama_karyawan', 'tanggal_masuk', 'waktu_masuk', 'status_masuk']);
    }

    public function test_update_absensi_berhasil(): void
    {
        $user = User::where('email', 'abian@gmail.com')->first();
        $this->assertNotNull($user, 'User abian@gmail.com tidak ditemukan di database');
        $this->actingAs($user);

        // Ambil data absensi yang sudah ada (dari seeder atau test sebelumnya)
        $absensi = AbsenKerja::where('nama_karyawan', 'Ujang')->first();
        $this->assertNotNull($absensi, 'Data absensi "Ujang" tidak ditemukan di database');

        $updateData = [
            'status_masuk' => 'sakit',
        ];

        $response = $this->patch(route('absensi.update', ['id' => $absensi->id]), $updateData);
        $response->assertStatus(302);

        $this->assertDatabaseHas('tbl_absen_kerja', [
            'id' => $absensi->id,
            'status_masuk' => 'sakit',
            'waktu_selesai_kerja' => '00:00:00',
        ]);
    }


    public function test_update_absensi_gagal(): void
    {
        $user = User::where('email', 'abian@gmail.com')->first();
        $this->assertNotNull($user, 'User abian@gmail.com tidak ditemukan di database');
        $this->actingAs($user);

        $absensi = AbsenKerja::create([
            'nama_karyawan' => 'Ujang',
            'tanggal_masuk' => '2025-04-17',
            'waktu_masuk' => '08:00:00',
            'status_masuk' => 'masuk',
        ]);

        $response = $this->patch(route('absensi.update', ['id' => $absensi->id]), [
            'status_masuk' => '', // Kosong, harusnya gagal
        ]);

        $response->assertSessionHasErrors(['status_masuk']);
    }
    public function test_hapus_absensi_berhasil(): void
    {
        $user = User::where('email', 'abian@gmail.com')->first();
        $this->assertNotNull($user, 'User abian@gmail.com tidak ditemukan di database');
        $this->actingAs($user);

        $absensi = AbsenKerja::create([
            'nama_karyawan' => 'Ujang',
            'tanggal_masuk' => '2025-04-17',
            'waktu_masuk' => '08:00:00',
            'status_masuk' => 'masuk',
        ]);

        $response = $this->delete(route('absensi.destroy', ['id' => $absensi->id]));
        $response->assertStatus(302);

        $this->assertDatabaseMissing('tbl_absen_kerja', ['id' => $absensi->id]);
    }
}
