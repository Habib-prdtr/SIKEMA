<?php

namespace App\Services;

use App\Models\Dispensasi;
use App\Models\JenisPenerimaan;
use App\Models\PosBiaya;
use App\Models\SaldoAwal;
use App\Models\Siswa;
use App\Models\SiswaTahunAjaran;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MasterDataService
{
    // --- SISWA ---
    public function getDaftarSiswa(Request $request): LengthAwarePaginator
    {
        $query = Siswa::query();
        $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($like, $cari) {
                $q->where('nama', $like, "%{$cari}%")
                    ->orWhere('no_induk', $like, "%{$cari}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $query->orderBy('nama')->paginate(20)->withQueryString();
    }

    public function cekSiswaPunyaTransaksi(Siswa $siswa): bool
    {
        return $siswa->tahunAjaran()
            ->whereHas('transaksi')
            ->exists();
    }

    public function cekNoIndukExist(string $noInduk, ?int $ignoreSiswaId = null): bool
    {
        $query = Siswa::where('no_induk', $noInduk);
        if ($ignoreSiswaId) {
            $query->where('id', '!=', $ignoreSiswaId);
        }
        return $query->exists();
    }

    public function simpanDataSiswa(array $data): \App\Models\Siswa
    {
        return \App\Models\Siswa::create($data);
    }

    public function updateDataSiswa(\App\Models\Siswa $siswa, array $data): bool
    {
        return $siswa->update($data);
    }

    public function hapusDataSiswa(\App\Models\Siswa $siswa): bool
    {
        return $siswa->delete();
    }

    public function importDataSiswa(array $rows): int
    {
        if (count($rows) <= 1) {
            throw new \Exception('File Excel kosong atau tidak memiliki baris data.');
        }

        $headers = array_map(function($h) {
            return strtolower(trim((string)$h));
        }, $rows[0]);

        $colIndex = [
            'no_induk' => -1, 'nama' => -1, 'kelas' => -1,
            'alamat' => -1, 'jenis_kelamin' => -1, 'tanggal_masuk' => -1,
        ];

        foreach ($headers as $index => $header) {
            if (str_contains($header, 'no_induk') || $header === 'nis' || $header === 'nisn' || str_contains($header, 'induk') || str_contains($header, 'nomor induk')) {
                $colIndex['no_induk'] = $index;
            } elseif (str_contains($header, 'nama') || str_contains($header, 'siswa')) {
                $colIndex['nama'] = $index;
            } elseif (str_contains($header, 'kelas')) {
                $colIndex['kelas'] = $index;
            } elseif (str_contains($header, 'alamat') || str_contains($header, 'almt') || str_contains($header, 'address')) {
                $colIndex['alamat'] = $index;
            } elseif (str_contains($header, 'jenis kelamin') || str_contains($header, 'jk') || str_contains($header, 'sex') || str_contains($header, 'gender') || str_contains($header, 'l/p')) {
                $colIndex['jenis_kelamin'] = $index;
            } elseif (str_contains($header, 'tanggal') || str_contains($header, 'tgl') || str_contains($header, 'masuk')) {
                $colIndex['tanggal_masuk'] = $index;
            }
        }

        if ($colIndex['no_induk'] === -1) $colIndex['no_induk'] = 0;
        if ($colIndex['nama'] === -1) $colIndex['nama'] = 1;
        if ($colIndex['kelas'] === -1) $colIndex['kelas'] = 2;
        if ($colIndex['alamat'] === -1) $colIndex['alamat'] = 3;
        if ($colIndex['jenis_kelamin'] === -1) $colIndex['jenis_kelamin'] = 4;
        if ($colIndex['tanggal_masuk'] === -1) $colIndex['tanggal_masuk'] = 5;

        $importedCount = 0;
        $errors = [];
        $processedNoInduk = [];

        return \Illuminate\Support\Facades\DB::transaction(function () use ($rows, $colIndex, &$importedCount, &$errors, &$processedNoInduk) {
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty(array_filter($row))) continue;

                $noInduk = trim((string)($row[$colIndex['no_induk']] ?? ''));
                $nama = trim((string)($row[$colIndex['nama']] ?? ''));
                $kelas = trim((string)($row[$colIndex['kelas']] ?? ''));
                $alamat = trim((string)($row[$colIndex['alamat']] ?? ''));
                $jkRaw = strtoupper(trim((string)($row[$colIndex['jenis_kelamin']] ?? '')));
                $tglMasukRaw = trim((string)($row[$colIndex['tanggal_masuk']] ?? ''));

                if (!$noInduk || !$nama || !$kelas) {
                    $errors[] = "Baris " . ($i + 1) . ": No Induk, Nama, dan Kelas wajib diisi.";
                    continue;
                }

                if (!preg_match('/^(7|8|9)/', $kelas)) {
                    $errors[] = "Baris " . ($i + 1) . ": Kelas '{$kelas}' tidak valid. Hanya diperbolehkan untuk tingkat kelas 7-9.";
                    continue;
                }

                // Cek duplikasi di database
                if (\App\Models\Siswa::where('no_induk', $noInduk)->exists()) {
                    $errors[] = "Baris " . ($i + 1) . ": Nomor induk '{$noInduk}' sudah terdaftar di database.";
                    continue;
                }

                // Cek duplikasi dalam berkas Excel itu sendiri
                if (in_array($noInduk, $processedNoInduk)) {
                    $errors[] = "Baris " . ($i + 1) . ": Nomor induk '{$noInduk}' ganda dalam berkas Excel.";
                    continue;
                }
                $processedNoInduk[] = $noInduk;

                $jk = 'L';
                if ($jkRaw === 'P' || str_contains(strtolower($jkRaw), 'perempuan') || str_contains(strtolower($jkRaw), 'putri') || str_contains(strtolower($jkRaw), 'p')) {
                    $jk = 'P';
                }

                $tanggalMasuk = null;
                if ($tglMasukRaw) {
                    try {
                        if (is_numeric($tglMasukRaw)) {
                            $tanggalMasuk = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tglMasukRaw)->format('Y-m-d');
                        } else {
                            $time = strtotime($tglMasukRaw);
                            if ($time) {
                                $tanggalMasuk = date('Y-m-d', $time);
                            } else {
                                $errors[] = "Baris " . ($i + 1) . ": Format tanggal masuk '{$tglMasukRaw}' tidak valid.";
                                continue;
                            }
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Baris " . ($i + 1) . ": Gagal membaca tanggal masuk '{$tglMasukRaw}'.";
                        continue;
                    }
                }

                \App\Models\Siswa::create([
                    'no_induk' => $noInduk,
                    'nama' => $nama,
                    'kelas' => $kelas,
                    'alamat' => $alamat ?: null,
                    'jenis_kelamin' => $jk,
                    'tanggal_masuk' => $tanggalMasuk,
                    'status' => 'aktif',
                ]);
                $importedCount++;
            }

            if (!empty($errors)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'import' => $errors
                ]);
            }

            return $importedCount;
        });
    }

    // --- JENIS PENERIMAAN ---
    public function getJenisPenerimaan(?TahunAjaran $tahunAktif): Collection
    {
        if (!$tahunAktif) return collect();
        return JenisPenerimaan::where('tahun_ajaran_id', $tahunAktif->id)
            ->withSum('tagihanIuran as total_terkumpul', 'terbayar')
            ->orderBy('urutan')
            ->get();
    }

    public function simpanJenisPenerimaan(array $data, \App\Services\TagihanService $tagihanService): \App\Models\JenisPenerimaan
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data, $tagihanService) {
            $jp = \App\Models\JenisPenerimaan::create($data);
            if ($jp->is_aktif) {
                $tagihanService->generateIuran($jp);
            }
            return $jp;
        });
    }

    public function updateJenisPenerimaan(\App\Models\JenisPenerimaan $jenisPenerimaan, array $data, \App\Services\TagihanService $tagihanService): bool
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($jenisPenerimaan, $data, $tagihanService) {
            $oldTarif = $jenisPenerimaan->tarif;
            $updated = $jenisPenerimaan->update($data);

            $tagihanService->syncIuran($jenisPenerimaan);

            if ($updated && isset($data['tarif']) && $data['tarif'] != $oldTarif) {
                \App\Models\TagihanIuran::where('jenis_penerimaan_id', $jenisPenerimaan->id)
                    ->where('terbayar', 0)
                    ->update(['tagihan' => $jenisPenerimaan->tarif]);
            }

            return $updated;
        });
    }

    public function hapusJenisPenerimaan(\App\Models\JenisPenerimaan $jenisPenerimaan): bool
    {
        return $jenisPenerimaan->delete();
    }

    public function cekJenisPenerimaanAdaPembayaran(JenisPenerimaan $jenisPenerimaan): bool
    {
        return $jenisPenerimaan->tagihanIuran()
            ->where('terbayar', '>', 0)
            ->exists();
    }

    // --- POS BIAYA ---
    public function getPosBiaya(?TahunAjaran $tahunAktif): Collection
    {
        if (!$tahunAktif) return collect();
        return PosBiaya::where('tahun_ajaran_id', $tahunAktif->id)
            ->orderBy('nama')
            ->get();
    }

    public function getPosBiayaWithSumPengeluaran(?TahunAjaran $tahunAktif): Collection
    {
        if (!$tahunAktif) return collect();
        return PosBiaya::where('tahun_ajaran_id', $tahunAktif->id)
            ->withSum('pengeluaran', 'jumlah')
            ->orderBy('nama')
            ->get();
    }

    public function cekPosBiayaAdaPengeluaran(PosBiaya $posBiaya): bool
    {
        return $posBiaya->pengeluaran()->exists();
    }

    public function simpanPosBiaya(array $data): \App\Models\PosBiaya
    {
        return \App\Models\PosBiaya::create($data);
    }

    public function updatePosBiaya(\App\Models\PosBiaya $posBiaya, array $data): bool
    {
        return $posBiaya->update($data);
    }

    public function hapusPosBiaya(\App\Models\PosBiaya $posBiaya): bool
    {
        return $posBiaya->delete();
    }

    // --- SALDO AWAL ---
    public function getSaldoAwalTahunAktif(?TahunAjaran $tahunAktif): ?SaldoAwal
    {
        if (!$tahunAktif) return null;
        return SaldoAwal::where('tahun_ajaran_id', $tahunAktif->id)->first();
    }

    public function getDaftarSaldoAwal(): Collection
    {
        return SaldoAwal::with('tahunAjaran')->orderByDesc('id')->get();
    }

    public function simpanSaldoAwal(array $data): \App\Models\SaldoAwal
    {
        return \App\Models\SaldoAwal::create($data);
    }

    public function updateSaldoAwal(\App\Models\SaldoAwal $saldoAwal, array $data): bool
    {
        return $saldoAwal->update($data);
    }

    public function getTahunList(): Collection
    {
        return TahunAjaran::orderByDesc('nama')->get();
    }

    public function simpanTahunAjaran(array $data): \App\Models\TahunAjaran
    {
        return \App\Models\TahunAjaran::create($data);
    }

    public function aktifkanTahunAjaran(\App\Models\TahunAjaran $tahunAjaran): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($tahunAjaran) {
            $currentActive = \App\Models\TahunAjaran::where('is_aktif', true)->first();

            // Kenaikan Kelas Otomatis ketika berganti ke tahun ajaran yang lebih baru
            if ($currentActive && $tahunAjaran->id > $currentActive->id) {
                $siswaList = \App\Models\Siswa::where('status', \App\Models\Siswa::STATUS_AKTIF)->get();
                foreach ($siswaList as $s) {
                    $currentKelas = $s->kelas;

                    // 1. Kenaikan kelas berbasis angka romawi (SMP/SMA/Sederajat)
                    if (preg_match('/^VIII(.*)/i', $currentKelas, $matches)) {
                        $s->update(['kelas' => 'IX' . $matches[1]]);
                    } elseif (preg_match('/^VII(.*)/i', $currentKelas, $matches)) {
                        $s->update(['kelas' => 'VIII' . $matches[1]]);
                    } elseif (preg_match('/^IX(.*)/i', $currentKelas, $matches)) {
                        $s->update(['status' => \App\Models\Siswa::STATUS_NONAKTIF]);
                    } elseif (preg_match('/^XI(.*)/i', $currentKelas, $matches)) {
                        $s->update(['kelas' => 'XII' . $matches[1]]);
                    } elseif (preg_match('/^X(.*)/i', $currentKelas, $matches)) {
                        $s->update(['kelas' => 'XI' . $matches[1]]);
                    } elseif (preg_match('/^XII(.*)/i', $currentKelas, $matches)) {
                        $s->update(['status' => \App\Models\Siswa::STATUS_NONAKTIF]);
                    }
                    // 2. Kenaikan kelas berbasis angka biasa
                    elseif (preg_match('/^7(.*)/', $currentKelas, $matches)) {
                        $s->update(['kelas' => '8' . $matches[1]]);
                    } elseif (preg_match('/^8(.*)/', $currentKelas, $matches)) {
                        $s->update(['kelas' => '9' . $matches[1]]);
                    } elseif (preg_match('/^9(.*)/', $currentKelas)) {
                        $s->update(['status' => \App\Models\Siswa::STATUS_NONAKTIF]);
                    }
                }
            }

            \App\Models\TahunAjaran::where('id', '!=', $tahunAjaran->id)
                ->update(['is_aktif' => false]);

            $tahunAjaran->update(['is_aktif' => true]);
        });
    }

    public function getSiswaTahunAjaranList(?TahunAjaran $tahunAktif, ?string $cari, ?string $kelas): LengthAwarePaginator
    {
        $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
        $query = Siswa::where('status', Siswa::STATUS_AKTIF);

        if ($cari) {
            $query->where(function ($q) use ($like,$cari) {
                $q->where('nama', $like, "%{$cari}%")
                    ->orWhere('no_induk', $like, "%{$cari}%");
            });
        }

        if ($kelas) {
            if (in_array($kelas, ['7', '8', '9'])) {
                $query->where('kelas', $like, "{$kelas}%");
            } else {
                $query->where('kelas', $kelas);
            }
        }

        return $query->with([
            'tahunAjaran' => function ($query) use ($tahunAktif) {
                $query->where('tahun_ajaran_id', $tahunAktif?->id)
                    ->with('transaksi.details');
            },
        ])->orderBy('nama')->paginate(20);
    }

    public function getDaftarKelasSiswa(): Collection
    {
        return \App\Models\Siswa::distinct()->orderBy('kelas')->pluck('kelas')->filter();
    }

    public function aktifkanSiswa(array $data, \App\Services\TagihanService $tagihanService): \App\Models\Siswa
    {
        $masterTarif = \App\Models\MasterTarifSpp::findOrFail($data['master_tarif_spp_id']);
        $tarifSpp = $masterTarif->tarif;

        \Illuminate\Support\Facades\DB::transaction(function () use ($data, $tarifSpp, $tagihanService) {
            $sta = SiswaTahunAjaran::create([
                'siswa_id' => $data['siswa_id'],
                'tahun_ajaran_id' => $data['tahun_ajaran_id'],
                'tarif_spp' => $tarifSpp,
                'dispensasi_id' => $data['dispensasi_id'] ?? null,
                'durasi_dispensasi' => $data['durasi_dispensasi'] ?? null,
                'tunggakan_awal' => $data['tunggakan_awal'] ?? 0,
            ]);

            $sta->load('tahunAjaran');
            $tagihanService->generateSpp($sta);
            $tagihanService->generateTabunganWajib($sta);
            $tagihanService->generateIuranUntukSiswa($sta);
        });

        return \App\Models\Siswa::find($data['siswa_id']);
    }

    public function aktifkanSemuaSiswa(array $data, \App\Services\TagihanService $tagihanService): int
    {
        $tahunAjaranId = $data['tahun_ajaran_id'];
        $kelas = $data['kelas'];
        $masterTarif = \App\Models\MasterTarifSpp::findOrFail($data['master_tarif_spp_id']);
        $tarifSpp = $masterTarif->tarif;

        $semuaSiswaAktifDiKelas = Siswa::where('status', Siswa::STATUS_AKTIF)
            ->where('kelas', $kelas)
            ->get();

        $count = 0;
        \Illuminate\Support\Facades\DB::transaction(function () use ($semuaSiswaAktifDiKelas, $tahunAjaranId, $tarifSpp, $tagihanService, &$count) {
            foreach ($semuaSiswaAktifDiKelas as $siswa) {
                if ($this->cekSiswaSudahAktifTahunIni($siswa->id, $tahunAjaranId)) {
                    continue;
                }

                $sta = SiswaTahunAjaran::create([
                    'siswa_id' => $siswa->id,
                    'tahun_ajaran_id' => $tahunAjaranId,
                    'tarif_spp' => $tarifSpp,
                    'tunggakan_awal' => 0,
                ]);

                $sta->load('tahunAjaran');
                $tagihanService->generateSpp($sta);
                $tagihanService->generateTabunganWajib($sta);
                $tagihanService->generateIuranUntukSiswa($sta);
                $count++;
            }
        });

        return $count;
    }

    public function updateSppSiswa(SiswaTahunAjaran $siswaTahunAjaran, array $data): void
    {
        $masterTarif = \App\Models\MasterTarifSpp::findOrFail($data['master_tarif_spp_id']);
        $tarifSpp = $masterTarif->tarif;
        $dispensasiId = $data['dispensasi_id'] ?? null;
        $durasiDispensasi = $data['durasi_dispensasi'] ?? null;

        \Illuminate\Support\Facades\DB::transaction(function () use ($siswaTahunAjaran, $tarifSpp, $dispensasiId, $durasiDispensasi) {
            $siswaTahunAjaran->update([
                'tarif_spp' => $tarifSpp,
                'dispensasi_id' => $dispensasiId,
                'durasi_dispensasi' => $durasiDispensasi,
            ]);

            // Load dispensasi relation
            $siswaTahunAjaran->loadMissing('dispensasi');
            $dispensasi = $siswaTahunAjaran->dispensasi;
            $durasi = $siswaTahunAjaran->durasi_dispensasi ?? 0;

            // Fetch SPP bills ordered chronologically
            $bills = $siswaTahunAjaran->tagihanSpp()->orderBy('tahun')->orderBy('bulan')->get();

            $dispensasiAppliedCount = 0;

            foreach ($bills as $bill) {
                if ($bill->status !== 'belum') {
                    continue; // Skip paid bills
                }

                $tagihanNominal = $tarifSpp;

                // Apply dispensation discount if inside the duration for unpaid bills
                if ($dispensasi && $dispensasiAppliedCount < $durasi) {
                    if ($dispensasi->tipe_potongan === 'persen') {
                        $potongan = ($tarifSpp * $dispensasi->nilai_potongan) / 100;
                        $tagihanNominal = max(0, $tarifSpp - $potongan);
                    } elseif ($dispensasi->tipe_potongan === 'nominal') {
                        $tagihanNominal = max(0, $tarifSpp - $dispensasi->nilai_potongan);
                    }
                    $dispensasiAppliedCount++;
                }

                $bill->update([
                    'tagihan' => $tagihanNominal,
                ]);
            }
        });
    }

    public function assignDispensasiKeSiswa(SiswaTahunAjaran $siswaTahunAjaran, ?int $dispensasiId, int $durasi, ?string $semester = 'semua'): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($siswaTahunAjaran, $dispensasiId, $durasi, $semester) {
            $sem = $semester ?? 'semua';
            $tarifSpp = $siswaTahunAjaran->tarif_spp;
            $dispensasi = $dispensasiId ? Dispensasi::find($dispensasiId) : null;

            // Reset iuran to full tarif for any non-matching iuran or when revoking dispensasi
            $resetIuranFull = function ($targetJpId = null) use ($siswaTahunAjaran) {
                $query = $siswaTahunAjaran->tagihanIuran()->where('terbayar', 0);
                if ($targetJpId !== null) {
                    $query->where('jenis_penerimaan_id', '!=', $targetJpId);
                }
                $tagihanIuranList = $query->with('jenisPenerimaan')->get();
                foreach ($tagihanIuranList as $ti) {
                    if ($ti->jenisPenerimaan) {
                        $ti->update(['tagihan' => $ti->jenisPenerimaan->tarif]);
                    }
                }
            };

            // Reset SPP to full tarif
            $resetSppFull = function () use ($siswaTahunAjaran, $tarifSpp) {
                foreach ($siswaTahunAjaran->tagihanSpp as $bill) {
                    if ($bill->status === 'belum') {
                        $bill->update(['tagihan' => $tarifSpp]);
                    }
                }
            };

            if (!$dispensasiId || $durasi <= 0 || !$dispensasi) {
                $siswaTahunAjaran->update([
                    'dispensasi_id' => null,
                    'durasi_dispensasi' => 0,
                ]);
                $resetSppFull();
                $resetIuranFull();
                return;
            }

            // Target is a specific Jenis Penerimaan (Iuran)
            if (!empty($dispensasi->jenis_penerimaan_id)) {
                $resetSppFull();
                $resetIuranFull($dispensasi->jenis_penerimaan_id);

                $targetIuran = $siswaTahunAjaran->tagihanIuran()
                    ->where('jenis_penerimaan_id', $dispensasi->jenis_penerimaan_id)
                    ->where('terbayar', 0)
                    ->with('jenisPenerimaan')
                    ->first();

                if ($targetIuran && $targetIuran->jenisPenerimaan) {
                    $origTarif = $targetIuran->jenisPenerimaan->tarif;
                    if ($dispensasi->tipe_potongan === 'persen') {
                        $potongan = ($origTarif * $dispensasi->nilai_potongan) / 100;
                        $tagihanNominal = max(0, $origTarif - $potongan);
                    } elseif ($dispensasi->tipe_potongan === 'nominal') {
                        $tagihanNominal = max(0, $origTarif - $dispensasi->nilai_potongan);
                    } else {
                        $tagihanNominal = $origTarif;
                    }
                    $targetIuran->update(['tagihan' => $tagihanNominal]);
                }

                $siswaTahunAjaran->update([
                    'dispensasi_id' => $dispensasiId,
                    'durasi_dispensasi' => $durasi,
                ]);
                return;
            }

            // Target is SPP (jenis_penerimaan_id is null)
            $resetIuranFull();

            $ganjilBulan = [7, 8, 9, 10, 11, 12];
            $genapBulan = [1, 2, 3, 4, 5, 6];

            if ($sem === 'ganjil') {
                $targetBulan = $ganjilBulan;
                $preserveBulan = $genapBulan;
            } elseif ($sem === 'genap') {
                $targetBulan = $genapBulan;
                $preserveBulan = $ganjilBulan;
            } else {
                $targetBulan = array_merge($ganjilBulan, $genapBulan);
                $preserveBulan = [];
            }

            $schoolMonthOrder = [7 => 1, 8 => 2, 9 => 3, 10 => 4, 11 => 5, 12 => 6, 1 => 7, 2 => 8, 3 => 9, 4 => 10, 5 => 11, 6 => 12];
            $bills = $siswaTahunAjaran->tagihanSpp()->get()->sortBy(function ($bill) use ($schoolMonthOrder) {
                return ($bill->tahun * 100) + ($schoolMonthOrder[(int) $bill->bulan] ?? (int) $bill->bulan);
            });
            $appliedInTarget = 0;

            foreach ($bills as $bill) {
                if ($bill->status !== 'belum') {
                    continue; // Skip paid bills
                }

                $bulan = (int) $bill->bulan;

                if (in_array($bulan, $preserveBulan, true)) {
                    // Preserve existing dispensation in non-target semester
                    continue;
                }

                if (in_array($bulan, $targetBulan, true)) {
                    if ($appliedInTarget < $durasi) {
                        if ($dispensasi->tipe_potongan === 'persen') {
                            $potongan = ($tarifSpp * $dispensasi->nilai_potongan) / 100;
                            $tagihanNominal = max(0, $tarifSpp - $potongan);
                        } elseif ($dispensasi->tipe_potongan === 'nominal') {
                            $tagihanNominal = max(0, $tarifSpp - $dispensasi->nilai_potongan);
                        } else {
                            $tagihanNominal = $tarifSpp;
                        }
                        $appliedInTarget++;
                    } else {
                        $tagihanNominal = $tarifSpp;
                    }

                    $bill->update(['tagihan' => $tagihanNominal]);
                }
            }

            // Recalculate total discounted months across the year
            $totalDiscountedMonths = $siswaTahunAjaran->tagihanSpp()
                ->where('tagihan', '<', $tarifSpp)
                ->count();

            $siswaTahunAjaran->update([
                'dispensasi_id' => $totalDiscountedMonths > 0 ? $dispensasiId : null,
                'durasi_dispensasi' => $totalDiscountedMonths,
            ]);
        });
    }

    public function updateDispensasi(\App\Models\Dispensasi $dispensasi, array $data): bool
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($dispensasi, $data) {
            $updated = $dispensasi->update($data);

            if ($updated) {
                $penerimaList = SiswaTahunAjaran::where('dispensasi_id', $dispensasi->id)->get();
                foreach ($penerimaList as $sta) {
                    $this->assignDispensasiKeSiswa($sta, $dispensasi->id, $sta->durasi_dispensasi ?? 0, $sta->semester_dispensasi ?? 'semua');
                }
            }

            return $updated;
        });
    }

    public function cekSiswaSudahAktifTahunIni(int $siswaId, int $tahunAjaranId): bool
    {
        return SiswaTahunAjaran::where('siswa_id', $siswaId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->exists();
    }

    // --- TARIF SPP ---
    public function getTarifSpp(?TahunAjaran $tahunAktif): Collection
    {
        if (!$tahunAktif) return collect();
        return \App\Models\MasterTarifSpp::where('tahun_ajaran_id', $tahunAktif->id)
            ->orderBy('kelas')
            ->get();
    }

    public function cekTarifSppExists(int $tahunAjaranId, string $kelas, ?int $ignoreId = null): bool
    {
        $query = \App\Models\MasterTarifSpp::where('tahun_ajaran_id', $tahunAjaranId)
            ->where('kelas', $kelas);
            
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        
        return $query->exists();
    }

    public function simpanTarifSpp(array $data): \App\Models\MasterTarifSpp
    {
        return \App\Models\MasterTarifSpp::create($data);
    }

    public function updateTarifSpp(\App\Models\MasterTarifSpp $tarifSpp, array $data): bool
    {
        $newTarif = $data['tarif'] ?? $tarifSpp->tarif;
        $tarifGrade = $this->getGradeFromKelas($tarifSpp->kelas);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($tarifSpp, $data, $newTarif, $tarifGrade) {
            $updated = $tarifSpp->update($data);

            if ($updated && $tarifGrade !== null) {
                // Cari dan perbarui siswa yang terdaftar pada grade/kelas yang sesuai di tahun ajaran yang sama
                $siswaTahunAjaranList = \App\Models\SiswaTahunAjaran::where('tahun_ajaran_id', $tarifSpp->tahun_ajaran_id)
                    ->with('siswa')
                    ->get();

                foreach ($siswaTahunAjaranList as $sta) {
                    $siswaKelas = $sta->siswa->kelas ?? null;
                    if ($siswaKelas && $this->getGradeFromKelas($siswaKelas) === $tarifGrade) {
                        $sta->update(['tarif_spp' => $newTarif]);
                        
                        $sta->tagihanSpp()
                            ->where('status', \App\Models\TagihanSpp::STATUS_BELUM)
                            ->update(['tagihan' => $newTarif]);
                    }
                }
            }

            return $updated;
        });
    }

    private function getGradeFromKelas(?string $kelas): ?int
    {
        if (!$kelas) return null;
        
        if (preg_match('/\d+/', $kelas, $matches)) {
            return (int) $matches[0];
        }
        
        $romanMap = [
            'viii' => 8, 'vii' => 7, 'xii' => 12, 'iii' => 3,
            'xi' => 11, 'ix' => 9, 'vi' => 6, 'ii' => 2,
            'iv' => 4, 'x' => 10, 'v' => 5, 'i' => 1
        ];
        
        $normalized = strtolower($kelas);
        foreach ($romanMap as $roman => $num) {
            if (strpos($normalized, $roman) !== false) {
                return $num;
            }
        }
        
        return null;
    }

    public function hapusTarifSpp(\App\Models\MasterTarifSpp $tarifSpp): bool
    {
        return $tarifSpp->delete();
    }

    // --- TABUNGAN WAJIB ---
    public function getTabunganWajib(?TahunAjaran $tahunAktif): Collection
    {
        if (!$tahunAktif) return collect();
        return \App\Models\MasterTabunganWajib::where('tahun_ajaran_id', $tahunAktif->id)
            ->orderBy('kelas')
            ->get();
    }

    public function cekTabunganWajibExists(int $tahunAjaranId, string $kelas, ?int $ignoreId = null): bool
    {
        $query = \App\Models\MasterTabunganWajib::where('tahun_ajaran_id', $tahunAjaranId)
            ->where('kelas', $kelas);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }

    public function simpanTabunganWajib(array $data): \App\Models\MasterTabunganWajib
    {
        return \App\Models\MasterTabunganWajib::create($data);
    }

    public function updateTabunganWajib(\App\Models\MasterTabunganWajib $tabunganWajib, array $data): bool
    {
        $newTarif = $data['tarif'] ?? $tabunganWajib->tarif;
        $tarifGrade = $this->getGradeFromKelas($tabunganWajib->kelas);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($tabunganWajib, $data, $newTarif, $tarifGrade) {
            $updated = $tabunganWajib->update($data);

            if ($updated && $tarifGrade !== null) {
                $siswaTahunAjaranList = \App\Models\SiswaTahunAjaran::where('tahun_ajaran_id', $tabunganWajib->tahun_ajaran_id)
                    ->with('siswa')
                    ->get();

                foreach ($siswaTahunAjaranList as $sta) {
                    $siswaKelas = $sta->siswa->kelas ?? null;
                    if ($siswaKelas && $this->getGradeFromKelas($siswaKelas) === $tarifGrade) {
                        $sta->tagihanTabunganWajib()
                            ->where('status', \App\Models\TagihanTabunganWajib::STATUS_BELUM)
                            ->update(['tagihan' => $newTarif]);
                    }
                }
            }

            return $updated;
        });
    }

    public function hapusTabunganWajib(\App\Models\MasterTabunganWajib $tabunganWajib): bool
    {
        return $tabunganWajib->delete();
    }

    public function getTarifTabunganWajibSiswa(\App\Models\SiswaTahunAjaran $sta): int
    {
        $list = \App\Models\MasterTabunganWajib::where('tahun_ajaran_id', $sta->tahun_ajaran_id)->get();
        if ($list->isEmpty()) {
            return 0;
        }

        $siswaKelas = $sta->siswa->kelas ?? '';
        $siswaGrade = $this->getGradeFromKelas($siswaKelas);

        foreach ($list as $tw) {
            if ($tw->kelas === $siswaKelas) {
                return $tw->tarif;
            }
            if ($siswaGrade !== null && $this->getGradeFromKelas($tw->kelas) === $siswaGrade) {
                return $tw->tarif;
            }
        }

        return $list->first()->tarif ?? 0;
    }
}



