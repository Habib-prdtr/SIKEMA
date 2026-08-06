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
        $dispensasiAppliedCount = 0;
        foreach ($bulanTahun as $bt) {
            $tagihanNominal = $sta->tarif_spp;

            // Jika dalam masa durasi dispensasi dan dispensasi memotong SPP (jenis_penerimaan_id null)
            if ($dispensasi && empty($dispensasi->jenis_penerimaan_id) && $dispensasiAppliedCount < $durasiDispensasi) {
                if ($dispensasi->tipe_potongan === 'persen') {
                    $potongan = ($sta->tarif_spp * $dispensasi->nilai_potongan) / 100;
                    $tagihanNominal = max(0, $sta->tarif_spp - $potongan);
                } elseif ($dispensasi->tipe_potongan === 'nominal') {
                    $tagihanNominal = max(0, $sta->tarif_spp - $dispensasi->nilai_potongan);
                }
                $dispensasiAppliedCount++;
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
     * Generate 12 tagihan Tabungan Wajib untuk siswa di tahun ajaran.
     */
    public function generateTabunganWajib(SiswaTahunAjaran $sta): void
    {
        $sta->loadMissing('tahunAjaran');
        $tahunAwal = (int) substr($sta->tahunAjaran->nama, 0, 4);

        $bulanTahun = [];
        for ($bulan = 7; $bulan <= 12; $bulan++) {
            $bulanTahun[] = ['bulan' => $bulan, 'tahun' => $tahunAwal];
        }
        for ($bulan = 1; $bulan <= 6; $bulan++) {
            $bulanTahun[] = ['bulan' => $bulan, 'tahun' => $tahunAwal + 1];
        }

        $masterDataService = app(MasterDataService::class);
        $tarif = $masterDataService->getTarifTabunganWajibSiswa($sta);

        $rows = [];
        foreach ($bulanTahun as $bt) {
            $exists = \App\Models\TagihanTabunganWajib::where('siswa_tahun_ajaran_id', $sta->id)
                ->where('bulan', $bt['bulan'])
                ->where('tahun', $bt['tahun'])
                ->exists();

            if (!$exists) {
                $rows[] = [
                    'siswa_tahun_ajaran_id' => $sta->id,
                    'bulan' => $bt['bulan'],
                    'tahun' => $bt['tahun'],
                    'tagihan' => $tarif,
                    'terbayar' => 0,
                    'status' => \App\Models\TagihanTabunganWajib::STATUS_BELUM,
                    'updated_at' => null,
                ];
            }
        }

        if (!empty($rows)) {
            \App\Models\TagihanTabunganWajib::insert($rows);
        }
    }

    /**
     * Generate tagihan iuran untuk semua siswa aktif di tahun ajaran yang sama.
     *
    /**
     * Generate tagihan iuran untuk semua siswa aktif di tahun ajaran yang sesuai kelas.
     *
     * @param  JenisPenerimaan  $jp  Jenis penerimaan yang baru diaktifkan / diperbarui
     */
    public function generateIuran(JenisPenerimaan $jp): void
    {
        $this->syncIuran($jp);
    }

    /**
     * Sinkronkan semua jenis penerimaan (iuran) di tahun ajaran tertentu.
     */
    public function syncSemuaIuranTahunAjaran(?int $tahunAjaranId): void
    {
        if (! $tahunAjaranId) return;

        $jenisPenerimaanList = JenisPenerimaan::where('tahun_ajaran_id', $tahunAjaranId)->get();
        foreach ($jenisPenerimaanList as $jp) {
            $this->syncIuran($jp);
        }
    }

    /**
     * Sinkronkan tagihan iuran berdasarkan kelas jenis penerimaan.
     * Hapus tagihan belum terbayar milik siswa yang tidak cocok kelasnya,
     * dan buat tagihan baru untuk siswa yang cocok kelasnya.
     */
    public function syncIuran(JenisPenerimaan $jp): void
    {
        // Ambil semua siswa yang aktif di tahun ajaran ini
        $allSiswa = SiswaTahunAjaran::with('siswa')
            ->where('tahun_ajaran_id', $jp->tahun_ajaran_id)
            ->get();

        $matchingStaIds = [];
        $nonMatchingStaIds = [];

        foreach ($allSiswa as $sta) {
            if ($jp->matchesKelas($sta->siswa->kelas ?? null)) {
                $matchingStaIds[] = $sta->id;
            } else {
                $nonMatchingStaIds[] = $sta->id;
            }
        }

        // Hapus tagihan iuran yang belum pernah dibayar (terbayar == 0) untuk siswa yang tidak sesuai kelasnya
        if (! empty($nonMatchingStaIds)) {
            TagihanIuran::where('jenis_penerimaan_id', $jp->id)
                ->whereIn('siswa_tahun_ajaran_id', $nonMatchingStaIds)
                ->where('terbayar', 0)
                ->delete();
        }

        // Jika iuran aktif, buat tagihan baru untuk siswa yang sesuai kelas tetapi belum punya tagihan
        if ($jp->is_aktif && ! empty($matchingStaIds)) {
            $sudahAda = TagihanIuran::where('jenis_penerimaan_id', $jp->id)
                ->whereIn('siswa_tahun_ajaran_id', $matchingStaIds)
                ->pluck('siswa_tahun_ajaran_id')
                ->toArray();

            $matchingStas = SiswaTahunAjaran::with('dispensasi')
                ->whereIn('id', $matchingStaIds)
                ->get()
                ->keyBy('id');

            $rows = [];
            foreach ($matchingStaIds as $staId) {
                if (in_array($staId, $sudahAda)) {
                    continue;
                }

                $staItem = $matchingStas[$staId] ?? null;
                $tagihanNominal = $jp->tarif;
                if ($staItem && $staItem->dispensasi && $staItem->dispensasi->jenis_penerimaan_id == $jp->id) {
                    $disp = $staItem->dispensasi;
                    if ($disp->tipe_potongan === 'persen') {
                        $pot = ($jp->tarif * $disp->nilai_potongan) / 100;
                        $tagihanNominal = max(0, $jp->tarif - $pot);
                    } elseif ($disp->tipe_potongan === 'nominal') {
                        $tagihanNominal = max(0, $jp->tarif - $disp->nilai_potongan);
                    }
                }

                $rows[] = [
                    'siswa_tahun_ajaran_id' => $staId,
                    'jenis_penerimaan_id' => $jp->id,
                    'tagihan' => $tagihanNominal,
                    'terbayar' => 0,
                    'status' => TagihanIuran::STATUS_BELUM,
                    'updated_at' => null,
                ];
            }

            if (! empty($rows)) {
                TagihanIuran::insert($rows);
            }
        }
    }

    /**
     * Generate tagihan iuran untuk siswa yang baru diaktifkan.
     * Membuat tagihan untuk semua jenis penerimaan (iuran) aktif di tahun ajaran tersebut
     * yang sesuai dengan kelas siswa.
     *
     * @param  SiswaTahunAjaran  $sta  Record siswa-tahun-ajaran yang baru dibuat
     */
    public function generateIuranUntukSiswa(SiswaTahunAjaran $sta): void
    {
        $sta->loadMissing(['siswa', 'dispensasi']);
        $siswaKelas = $sta->siswa->kelas ?? null;

        // Ambil semua jenis penerimaan (iuran) yang aktif di tahun ajaran ini
        $jenisPenerimaan = JenisPenerimaan::where('tahun_ajaran_id', $sta->tahun_ajaran_id)
            ->where('is_aktif', true)
            ->get();

        $rows = [];
        foreach ($jenisPenerimaan as $jp) {
            if (! $jp->matchesKelas($siswaKelas)) {
                continue;
            }

            // Cek apakah sudah ada tagihan untuk iuran ini (untuk menghindari duplikasi)
            $exists = TagihanIuran::where('siswa_tahun_ajaran_id', $sta->id)
                ->where('jenis_penerimaan_id', $jp->id)
                ->exists();

            if (! $exists) {
                $tagihanNominal = $jp->tarif;
                if ($sta->dispensasi && $sta->dispensasi->jenis_penerimaan_id == $jp->id) {
                    $disp = $sta->dispensasi;
                    if ($disp->tipe_potongan === 'persen') {
                        $pot = ($jp->tarif * $disp->nilai_potongan) / 100;
                        $tagihanNominal = max(0, $jp->tarif - $pot);
                    } elseif ($disp->tipe_potongan === 'nominal') {
                        $tagihanNominal = max(0, $jp->tarif - $disp->nilai_potongan);
                    }
                }

                $rows[] = [
                    'siswa_tahun_ajaran_id' => $sta->id,
                    'jenis_penerimaan_id' => $jp->id,
                    'tagihan' => $tagihanNominal,
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

