<?php

namespace App\Services;

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
            'asrama' => -1, 'jenis_kelamin' => -1, 'tanggal_masuk' => -1,
        ];

        foreach ($headers as $index => $header) {
            if (str_contains($header, 'no_induk') || $header === 'nis' || $header === 'nisn' || str_contains($header, 'induk') || str_contains($header, 'nomor induk')) {
                $colIndex['no_induk'] = $index;
            } elseif (str_contains($header, 'nama') || str_contains($header, 'siswa')) {
                $colIndex['nama'] = $index;
            } elseif (str_contains($header, 'kelas')) {
                $colIndex['kelas'] = $index;
            } elseif (str_contains($header, 'asrama')) {
                $colIndex['asrama'] = $index;
            } elseif (str_contains($header, 'jenis kelamin') || str_contains($header, 'jk') || str_contains($header, 'sex') || str_contains($header, 'gender') || str_contains($header, 'l/p')) {
                $colIndex['jenis_kelamin'] = $index;
            } elseif (str_contains($header, 'tanggal') || str_contains($header, 'tgl') || str_contains($header, 'masuk')) {
                $colIndex['tanggal_masuk'] = $index;
            }
        }

        if ($colIndex['no_induk'] === -1) $colIndex['no_induk'] = 0;
        if ($colIndex['nama'] === -1) $colIndex['nama'] = 1;
        if ($colIndex['kelas'] === -1) $colIndex['kelas'] = 2;
        if ($colIndex['asrama'] === -1) $colIndex['asrama'] = 3;
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
                $asrama = trim((string)($row[$colIndex['asrama']] ?? ''));
                $jkRaw = strtoupper(trim((string)($row[$colIndex['jenis_kelamin']] ?? '')));
                $tglMasukRaw = trim((string)($row[$colIndex['tanggal_masuk']] ?? ''));

                if (!$noInduk || !$nama || !$kelas) {
                    $errors[] = "Baris " . ($i + 1) . ": No Induk, Nama, dan Kelas wajib diisi.";
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
                    'asrama' => $asrama ?: null,
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
            $statusSebelumnya = $jenisPenerimaan->is_aktif;
            $updated = $jenisPenerimaan->update($data);

            if (! $statusSebelumnya && $jenisPenerimaan->is_aktif) {
                $tagihanService->generateIuran($jenisPenerimaan);
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
            $query->where('kelas', $kelas);
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
                'tunggakan_awal' => $data['tunggakan_awal'] ?? 0,
            ]);

            $sta->load('tahunAjaran');
            $tagihanService->generateSpp($sta);
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

        \Illuminate\Support\Facades\DB::transaction(function () use ($siswaTahunAjaran, $tarifSpp) {
            $siswaTahunAjaran->update([
                'tarif_spp' => $tarifSpp,
            ]);

            $siswaTahunAjaran->tagihanSpp()
                ->where('status', 'belum')
                ->update([
                    'tagihan' => $tarifSpp,
                ]);
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
        return $tarifSpp->update($data);
    }

    public function hapusTarifSpp(\App\Models\MasterTarifSpp $tarifSpp): bool
    {
        return $tarifSpp->delete();
    }
}


