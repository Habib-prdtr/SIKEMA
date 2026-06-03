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
            $tahunAktif = TahunAjaran::create([
                'nama' => '2024/2025',
                'is_aktif' => true,
            ]);

            // 2. Data Siswa
            $siswaList = [
                ['no_induk' => '12345', 'nama' => 'Ahmad Fulan', 'kelas' => '7A', 'status' => 'aktif', 'jenis_kelamin' => 'L'],
                ['no_induk' => '12346', 'nama' => 'Budi Santoso', 'kelas' => '7A', 'status' => 'aktif', 'jenis_kelamin' => 'L'],
                ['no_induk' => '12347', 'nama' => 'Siti Aminah', 'kelas' => '8B', 'status' => 'aktif', 'jenis_kelamin' => 'P'],
                ['no_induk' => '12348', 'nama' => 'Dewi Lestari', 'kelas' => '9C', 'status' => 'aktif', 'jenis_kelamin' => 'P'],
            ];

            foreach ($siswaList as $siswaData) {
                $siswa = Siswa::create($siswaData);

                // Daftarkan siswa ke tahun ajaran aktif
                SiswaTahunAjaran::create([
                    'siswa_id' => $siswa->id,
                    'tahun_ajaran_id' => $tahunAktif->id,
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
                JenisPenerimaan::create($jp);
            }

            // 4. Pos Biaya (untuk pengeluaran)
            $posBiaya = [
                ['nama' => 'Listrik', 'anggaran' => 12000000, 'tahun_ajaran_id' => $tahunAktif->id],
                ['nama' => 'Gaji Guru', 'anggaran' => 150000000, 'tahun_ajaran_id' => $tahunAktif->id],
                ['nama' => 'Beli Alat Tulis', 'anggaran' => 5000000, 'tahun_ajaran_id' => $tahunAktif->id],
                ['nama' => 'Qurban / Acara', 'anggaran' => 25000000, 'tahun_ajaran_id' => $tahunAktif->id],
            ];

            foreach ($posBiaya as $pb) {
                PosBiaya::create($pb);
            }

            // 5. Saldo Awal Tahun
            SaldoAwal::create([
                'tahun_ajaran_id' => $tahunAktif->id,
                'jumlah' => 150000000, // Rp 150 juta saldo awal
                'keterangan' => 'Sisa saldo dari tahun ajaran 2023/2024',
            ]);
        });
    }
}
