<?php

namespace Database\Seeders;

use App\Models\JenisPenerimaan;
use App\Models\PosBiaya;
use App\Models\SaldoAwal;
use App\Models\Siswa;
use App\Models\SiswaTahunAjaran;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Tahun Ajaran
            $tahunAktif = TahunAjaran::firstOrCreate(
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

            // 2. Data Siswa
            $siswaList = [
                ['no_induk' => '12345', 'nama' => 'Ahmad Fulan', 'kelas' => '7A', 'status' => 'aktif', 'jenis_kelamin' => 'L'],
                ['no_induk' => '12346', 'nama' => 'Budi Santoso', 'kelas' => '7A', 'status' => 'aktif', 'jenis_kelamin' => 'L'],
                ['no_induk' => '12347', 'nama' => 'Siti Aminah', 'kelas' => '8B', 'status' => 'aktif', 'jenis_kelamin' => 'P'],
                ['no_induk' => '12348', 'nama' => 'Dewi Lestari', 'kelas' => '9C', 'status' => 'aktif', 'jenis_kelamin' => 'P'],
            ];

            foreach ($siswaList as $siswaData) {
                $siswa = Siswa::firstOrCreate(
                    ['no_induk' => $siswaData['no_induk']],
                    [
                        'nama' => $siswaData['nama'],
                        'kelas' => $siswaData['kelas'],
                        'status' => $siswaData['status'],
                        'jenis_kelamin' => $siswaData['jenis_kelamin']
                    ]
                );

                // Daftarkan siswa ke tahun ajaran aktif
                SiswaTahunAjaran::firstOrCreate([
                    'siswa_id' => $siswa->id,
                    'tahun_ajaran_id' => $tahunAktif->id,
                ], [
                    'tarif_spp' => 150000,
                    'tunggakan_awal' => $siswa->nama == 'Siti Aminah' ? 300000 : 0, // Siti punya tunggakan
                ]);
            }

            // 3. Jenis Penerimaan (selain SPP)
            $jenisPenerimaan = [
                ['nama' => 'Uang Gedung', 'tarif' => 2000000, 'tahun_ajaran_id' => $tahunAktif->id, 'urutan' => 1],
                ['nama' => 'Seragam Sekolah', 'tarif' => 800000, 'tahun_ajaran_id' => $tahunAktif->id, 'urutan' => 2],
                ['nama' => 'Kegiatan Ekstrakurikuler', 'tarif' => 150000, 'tahun_ajaran_id' => $tahunAktif->id, 'urutan' => 3],
            ];

            foreach ($jenisPenerimaan as $jp) {
                JenisPenerimaan::firstOrCreate([
                    'tahun_ajaran_id' => $jp['tahun_ajaran_id'],
                    'urutan' => $jp['urutan']
                ], [
                    'nama' => $jp['nama'],
                    'tarif' => $jp['tarif']
                ]);
            }

            // 4. Pos Biaya (untuk pengeluaran)
            $posBiaya = [
                ['nama' => 'Listrik', 'anggaran' => 12000000, 'tahun_ajaran_id' => $tahunAktif->id],
                ['nama' => 'Gaji Guru', 'anggaran' => 150000000, 'tahun_ajaran_id' => $tahunAktif->id],
                ['nama' => 'Beli Alat Tulis', 'anggaran' => 5000000, 'tahun_ajaran_id' => $tahunAktif->id],
                ['nama' => 'Qurban / Acara', 'anggaran' => 25000000, 'tahun_ajaran_id' => $tahunAktif->id],
            ];

            foreach ($posBiaya as $pb) {
                PosBiaya::firstOrCreate([
                    'nama' => $pb['nama'],
                    'tahun_ajaran_id' => $pb['tahun_ajaran_id']
                ], [
                    'anggaran' => $pb['anggaran']
                ]);
            }

            // 5. Saldo Awal Tahun
            SaldoAwal::firstOrCreate([
                'tahun_ajaran_id' => $tahunAktif->id
            ], [
                'jumlah' => 150000000, // Rp 150 juta saldo awal
                'keterangan' => 'Sisa saldo dari tahun ajaran 2023/2024',
            ]);
        });
    }
}
