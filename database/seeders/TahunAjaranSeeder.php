<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tahun Ajaran Aktif
        TahunAjaran::firstOrCreate(
            ['nama' => '2024/2025'],
            ['is_aktif' => true]
        );

        // Populate Tahun Ajaran dari 2025/2026 sampai 2049/2050
        for ($year = 2025; $year <= 2049; $year++) {
            $nextYear = $year + 1;
            TahunAjaran::firstOrCreate(
                ['nama' => "{$year}/{$nextYear}"],
                ['is_aktif' => false]
            );
        }
    }
}
