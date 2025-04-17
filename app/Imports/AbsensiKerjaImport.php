<?php

namespace App\Imports;

use App\Models\AbsenKerja;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AbsensiKerjaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cek jika nama_karyawan kosong
        if (empty($row['nama_karyawan'])) {
            return null;
        }

        try {
            // Cek dan dapatkan user berdasarkan nama karyawan
            $user = User::where('name', $row['nama_karyawan'])->first();

            // Jika user tidak ditemukan, return null
            if (!$user) {
                return null;
            }

            // Parsing tanggal dan waktu
            $tanggalMasuk = \Carbon\Carbon::parse($row['tanggal_masuk']);
            $waktuMasuk = \Carbon\Carbon::parse($row['waktu_masuk']);

            $rawWaktuSelesai = $row['waktu_selesai_kerja'] ?? null;

            if ($row['status_masuk'] !== 'Masuk') {
                $waktuSelesaiKerja = '00:00:00';
            } else {
                if (
                    empty($rawWaktuSelesai) ||
                    $rawWaktuSelesai === '00:00:00' ||
                    $rawWaktuSelesai === '00.00.00' ||
                    \Carbon\Carbon::parse($rawWaktuSelesai)->format('H:i:s') === '00:00:00'
                ) {
                    $waktuSelesaiKerja = null;
                } else {
                    $waktuSelesaiKerja = \Carbon\Carbon::parse($rawWaktuSelesai);
                }
            }
        } catch (\Exception $e) {
            return null;
        }

        // Menyimpan absensi dengan user_id
        return new AbsenKerja([
            'user_id' => $user->id, // Menyimpan user_id, bukan nama_karyawan
            'tanggal_masuk' => $tanggalMasuk,
            'waktu_masuk' => $waktuMasuk,
            'status_masuk' => $row['status_masuk'],
            'waktu_selesai_kerja' => $waktuSelesaiKerja,
        ]);
    }
}
