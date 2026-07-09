<?php

namespace App\Services;

use App\Models\JenisPenerimaan;
use App\Models\SiswaTahunAjaran;
use App\Models\TagihanIuran;
use App\Models\TagihanSpp;

class TagihanService
{
    /**
     * Generate 12 tagihan SPP untuk siswa yang baru diaktifkan di tahun ajaran.
     *
     * Dipanggil saat operator mengaktifkan siswa ke tahun ajaran tertentu.
     * JANGAN panggil langsung dari Controller — panggil lewat SiswaTahunAjaranController.
     *
     * @param  SiswaTahunAjaran  $sta  Record siswa-tahun-ajaran yang baru dibuat
     */
    public function generateSpp(SiswaTahunAjaran $sta): void
    {
        // Ambil tahun akademik dari nama tahun ajaran (mis: "2024/2025" → tahun awal = 2024)
        $tahunAwal = (int) substr($sta->tahunAjaran->nama, 0, 4);

        /*
         * Kalender akademik madrasah:
         * Semester 1: Juli (7) - Desember (12) → tahun awal
         * Semester 2: Januari (1) - Juni (6)   → tahun awal + 1
         */
        $bulanTahun = [];
        for ($bulan = 7; $bulan <= 12; $bulan++) {
            $bulanTahun[] = ['bulan' => $bulan, 'tahun' => $tahunAwal];
        }
        for ($bulan = 1; $bulan <= 6; $bulan++) {
            $bulanTahun[] = ['bulan' => $bulan, 'tahun' => $tahunAwal + 1];
        }

        // Load dispensasi jika ada
        $sta->loadMissing('dispensasi');
        $dispensasi = $sta->dispensasi;
        $durasiDispensasi = $sta->durasi_dispensasi ?? 0;

        $rows = [];
        foreach ($bulanTahun as $index => $bt) {
            $tagihanNominal = $sta->tarif_spp;

            // Jika dalam masa durasi dispensasi, potong tagihan SPP
            if ($dispensasi && $index < $durasiDispensasi) {
                if ($dispensasi->tipe_potongan === 'persen') {
                    $potongan = ($sta->tarif_spp * $dispensasi->nilai_potongan) / 100;
                    $tagihanNominal = max(0, $sta->tarif_spp - $potongan);
                } elseif ($dispensasi->tipe_potongan === 'nominal') {
                    $tagihanNominal = max(0, $sta->tarif_spp - $dispensasi->nilai_potongan);
                }
            }

            $rows[] = [
                'siswa_tahun_ajaran_id' => $sta->id,
                'bulan' => $bt['bulan'],
                'tahun' => $bt['tahun'],
                'tagihan' => $tagihanNominal,
                'terbayar' => 0,
                'status' => TagihanSpp::STATUS_BELUM,
                'updated_at' => null,
            ];
        }

        TagihanSpp::insert($rows);
    }

    /**
     * Generate tagihan iuran untuk semua siswa aktif di tahun ajaran yang sama.
     *
     * Dipanggil saat operator menambah/mengaktifkan jenis penerimaan (iuran) baru.
     * Hanya dibuat untuk siswa yang belum punya tagihan iuran tersebut.
     *
     * @param  JenisPenerimaan  $jp  Jenis penerimaan yang baru diaktifkan
     */
    public function generateIuran(JenisPenerimaan $jp): void
    {
        // Ambil semua siswa yang aktif di tahun ajaran ini
        $siswaTahunAjaran = SiswaTahunAjaran::where('tahun_ajaran_id', $jp->tahun_ajaran_id)
            ->pluck('id');

        // Cek siswa yang sudah punya tagihan iuran ini (hindari duplikasi)
        $sudahAda = TagihanIuran::where('jenis_penerimaan_id', $jp->id)
            ->whereIn('siswa_tahun_ajaran_id', $siswaTahunAjaran)
            ->pluck('siswa_tahun_ajaran_id')
            ->toArray();

        $rows = [];
        foreach ($siswaTahunAjaran as $staId) {
            if (in_array($staId, $sudahAda)) {
                continue; // Skip jika sudah ada
            }

            $rows[] = [
                'siswa_tahun_ajaran_id' => $staId,
                'jenis_penerimaan_id' => $jp->id,
                'tagihan' => $jp->tarif,
                'terbayar' => 0,
                'status' => TagihanIuran::STATUS_BELUM,
                'updated_at' => null,
            ];
        }

        if (! empty($rows)) {
            TagihanIuran::insert($rows);
        }
    }

    /**
     * Generate tagihan iuran untuk siswa yang baru diaktifkan.
     * Membuat tagihan untuk semua jenis penerimaan (iuran) aktif di tahun ajaran tersebut.
     *
     * @param  SiswaTahunAjaran  $sta  Record siswa-tahun-ajaran yang baru dibuat
     */
    public function generateIuranUntukSiswa(SiswaTahunAjaran $sta): void
    {
        // Ambil semua jenis penerimaan (iuran) yang aktif di tahun ajaran ini
        $jenisPenerimaan = JenisPenerimaan::where('tahun_ajaran_id', $sta->tahun_ajaran_id)
            ->where('is_aktif', true)
            ->get();

        $rows = [];
        foreach ($jenisPenerimaan as $jp) {
            // Cek apakah sudah ada tagihan untuk iuran ini (untuk menghindari duplikasi)
            $exists = TagihanIuran::where('siswa_tahun_ajaran_id', $sta->id)
                ->where('jenis_penerimaan_id', $jp->id)
                ->exists();

            if (!$exists) {
                $rows[] = [
                    'siswa_tahun_ajaran_id' => $sta->id,
                    'jenis_penerimaan_id' => $jp->id,
                    'tagihan' => $jp->tarif,
                    'terbayar' => 0,
                    'status' => TagihanIuran::STATUS_BELUM,
                    'updated_at' => null,
                ];
            }
        }

        if (! empty($rows)) {
            TagihanIuran::insert($rows);
        }
    }
}

