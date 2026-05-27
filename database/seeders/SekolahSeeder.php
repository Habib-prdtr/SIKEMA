<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use Illuminate\Database\Seeder;

class SekolahSeeder extends Seeder
{
    /**
     * Buat data sekolah default (placeholder — diubah via Pengaturan > Sekolah).
     */
    public function run(): void
    {
        if (! Sekolah::exists()) {
            Sekolah::create([
                'nama_sekolah' => 'Madrasah Aliyah ...',
                'nama_yayasan' => null,
                'alamat'       => null,
                'telepon'      => null,
                'email'        => null,
                'kepala_tu'    => null,
                'nip_kepala_tu' => null,
            ]);
        }
    }
}
