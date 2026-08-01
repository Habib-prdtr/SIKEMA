<?php

namespace App\Services;

use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PengaturanService
{
    /**
     * Dapatkan data profil sekolah saat ini.
     */
    public function getSekolah(): ?Sekolah
    {
        return Sekolah::getData();
    }

    /**
     * Simpan atau update data profil sekolah.
     *
     * @param array $data Data sekolah yang sudah divalidasi
     */
    public function simpanSekolah(array $data): void
    {
        $sekolah = Sekolah::getData();

        if ($sekolah) {
            $sekolah->update($data);
        } else {
            Sekolah::create($data);
        }
    }

    /**
     * Update password user (operator).
     *
     * @param User $user User yang sedang login
     * @param string $newPassword Password baru (belum di-hash)
     */
    public function updatePassword(User $user, string $newPassword): void
    {
        $user->update([
            'password' => Hash::make($newPassword)
        ]);
    }

    /**
     * Update fokus jenjang kelas aktif.
     */
    public function updateJenjangKelas(string $jenjang): void
    {
        $valid = in_array($jenjang, ['semua', '7', '8', '9']) ? $jenjang : 'semua';
        $sekolah = Sekolah::getData();
        if ($sekolah) {
            $sekolah->update(['jenjang_kelas_aktif' => $valid]);
        }
        session(['jenjang_kelas_aktif' => $valid]);
    }
}
