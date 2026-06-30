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

    // --- JENIS PENERIMAAN ---
    public function getJenisPenerimaan(?TahunAjaran $tahunAktif): Collection
    {
        if (!$tahunAktif) return collect();
        return JenisPenerimaan::where('tahun_ajaran_id', $tahunAktif->id)
            ->orderBy('urutan')
            ->get();
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

    public function getTahunList(): Collection
    {
        return TahunAjaran::orderByDesc('nama')->get();
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

    public function cekSiswaSudahAktifTahunIni(int $siswaId, int $tahunAjaranId): bool
    {
        return SiswaTahunAjaran::where('siswa_id', $siswaId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->exists();
    }
}


