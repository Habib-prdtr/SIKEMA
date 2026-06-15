<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DummyDataSeeder extends Seeder
{
    /**
     * Seed seluruh dummy data dari file SQL.
     *
     * Data yang di-seed:
     * - 3 tahun ajaran (2023/2024, 2024/2025, 2025/2026)
     * - 300 siswa (3 angkatan: 2023, 2024, 2025)
     * - 9 master tarif SPP
     * - 9 jenis penerimaan (3 per tahun ajaran)
     * - 600 siswa_tahun_ajaran (akumulatif)
     * - 7200 tagihan SPP (600 STA x 12 bulan)
     * - 1800 tagihan iuran (600 STA x 3 jenis)
     * - 3 users (admin, bendahara, staff_tu)
     * - 8036 transaksi + 8114 transaksi_detail
     * - 1 sekolah
     * - 30 pos_biaya (10 per tahun ajaran)
     * - 273 pengeluaran
     * - 3 saldo_awal
     *
     * Usage:
     *   php artisan db:seed --class=DummyDataSeeder
     */
    public function run(): void
    {
        $sqlFile = database_path('seeders/sql/dummy_data.sql');

        if (! file_exists($sqlFile)) {
            $this->command->error("File SQL tidak ditemukan: {$sqlFile}");
            return;
        }

        $this->command->info('Mengeksekusi dummy data SQL (' . round(filesize($sqlFile) / 1024 / 1024, 1) . ' MB)...');

        $sql = file_get_contents($sqlFile);

        // Eksekusi seluruh SQL dalam satu batch (sudah dibungkus BEGIN/COMMIT di file)
        DB::unprepared($sql);

        $this->command->info('Data dummy berhasil di-import.');

        // Fix password users agar bisa dipakai login (password: admin123)
        $this->fixUserPasswords();

        // Reset sequence untuk PostgreSQL agar tidak konflik
        $this->resetSequences();

        $this->printSummary();
    }

    /**
     * Fix password users karena SQL dummy menggunakan placeholder hash.
     */
    private function fixUserPasswords(): void
    {
        $password = Hash::make('admin123');

        DB::table('users')->update(['password' => $password]);

        $this->command->info('Password semua user direset ke: admin123');
    }

    /**
     * Reset sequence PostgreSQL agar auto-increment tidak konflik.
     */
    private function resetSequences(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $sequences = [
            'tahun_ajaran_id_seq' => 'tahun_ajaran',
            'siswa_id_seq' => 'siswa',
            'siswa_tahun_ajaran_id_seq' => 'siswa_tahun_ajaran',
            'master_tarif_spp_id_seq' => 'master_tarif_spp',
            'jenis_penerimaan_id_seq' => 'jenis_penerimaan',
            'tagihan_spp_id_seq' => 'tagihan_spp',
            'tagihan_iuran_id_seq' => 'tagihan_iuran',
            'transaksi_id_seq' => 'transaksi',
            'transaksi_detail_id_seq' => 'transaksi_detail',
            'pos_biaya_id_seq' => 'pos_biaya',
            'pengeluaran_id_seq' => 'pengeluaran',
            'saldo_awal_id_seq' => 'saldo_awal',
            'users_id_seq' => 'users',
        ];

        foreach ($sequences as $seq => $table) {
            DB::statement("SELECT setval('public.{$seq}', (SELECT MAX(id) FROM public.{$table}))");
        }
    }

    /**
     * Tampilkan ringkasan data yang di-seed.
     */
    private function printSummary(): void
    {
        $tables = [
            'tahun_ajaran', 'siswa', 'siswa_tahun_ajaran',
            'master_tarif_spp', 'jenis_penerimaan',
            'tagihan_spp', 'tagihan_iuran',
            'transaksi', 'transaksi_detail',
            'pengeluaran', 'pos_biaya', 'saldo_awal',
            'users', 'sekolah',
        ];

        $this->command->newLine();
        $this->command->info('=== Ringkasan Data Dummy ===');

        foreach ($tables as $table) {
            $count = DB::table($table)->count();
            $this->command->line(sprintf('  %-25s : %s records', $table, number_format($count)));
        }

        $this->command->newLine();
        $this->command->info('Login: username=admin, password=admin123');
    }
}
