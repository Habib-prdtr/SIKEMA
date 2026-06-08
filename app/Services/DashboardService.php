<?php

namespace App\Services;

use App\Models\Pengeluaran;
use App\Models\SaldoAwal;
use App\Models\SiswaTahunAjaran;
use App\Models\TagihanSpp;
use App\Models\TahunAjaran;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    protected TunggakanService $tunggakanService;

    public function __construct(TunggakanService $tunggakanService)
    {
        $this->tunggakanService = $tunggakanService;
    }

    public function getJumlahSiswa(?TahunAjaran $tahunAktif): int
    {
        if (!$tahunAktif) return 0;
        return SiswaTahunAjaran::where('tahun_ajaran_id', $tahunAktif->id)->count();
    }

    public function getTotalPenerimaanBulanIni(?TahunAjaran $tahunAktif): int
    {
        if (!$tahunAktif) return 0;
        return Transaksi::whereHas('siswaTahunAjaran', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('total_bayar');
    }

    public function getTotalPengeluaranBulanIni(?TahunAjaran $tahunAktif): int
    {
        if (!$tahunAktif) return 0;
        return Pengeluaran::whereHas('posBiaya', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('jumlah');
    }

    public function getTunggakanData(?TahunAjaran $tahunAktif): array
    {
        if (!$tahunAktif) {
            return ['siswaAdaTunggakan' => 0, 'totalTunggakanAwal' => 0];
        }

        $siswaDenganTunggakan = SiswaTahunAjaran::where('tahun_ajaran_id', $tahunAktif->id)
            ->where('tunggakan_awal', '>', 0)
            ->with('transaksi.details')
            ->get();

        $siswaAdaTunggakan = 0;
        $totalTunggakanAwal = 0;

        foreach ($siswaDenganTunggakan as $sta) {
            $sisa = $this->tunggakanService->hitungSisa($sta);
            if ($sisa > 0) {
                $siswaAdaTunggakan++;
                $totalTunggakanAwal += $sisa;
            }
        }

        return compact('siswaAdaTunggakan', 'totalTunggakanAwal');
    }

    public function getSppBelumLunasBulanIni(?TahunAjaran $tahunAktif): int
    {
        if (!$tahunAktif) return 0;
        return TagihanSpp::whereHas('siswaTahunAjaran', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->where('bulan', now()->month)
            ->where('tahun', now()->year)
            ->whereIn('status', [TagihanSpp::STATUS_BELUM, TagihanSpp::STATUS_CICILAN])
            ->count();
    }

    public function getTotalSaldo(?TahunAjaran $tahunAktif): int
    {
        if (!$tahunAktif) return 0;

        // Ambil semua tahun ajaran yang secara kronologis (nama) kurang dari atau sama dengan tahun aktif saat ini
        $tahunIds = TahunAjaran::where('nama', '<=', $tahunAktif->nama)->pluck('id');

        $saldoAwal = SaldoAwal::whereIn('tahun_ajaran_id', $tahunIds)->sum('jumlah');
        $totalPenerimaan = Transaksi::whereHas('siswaTahunAjaran', fn ($q) => $q->whereIn('tahun_ajaran_id', $tahunIds))->sum('total_bayar');
        $totalPengeluaran = Pengeluaran::whereHas('posBiaya', fn ($q) => $q->whereIn('tahun_ajaran_id', $tahunIds))->sum('jumlah');

        return $saldoAwal + $totalPenerimaan - $totalPengeluaran;
    }

    public function getGrafikBulanan(?TahunAjaran $tahunAktif): array
    {
        $bulanLabels = collect();
        $dataPenerimaan = collect();
        $dataPengeluaran = collect();

        if ($tahunAktif) {
            for ($i = 5; $i >= 0; $i--) {
                $bulan = now()->subMonths($i)->month;
                $tahun = now()->subMonths($i)->year;
                $label = now()->subMonths($i)->locale('id')->isoFormat('MMM');

                $pemasukan = Transaksi::whereHas('siswaTahunAjaran', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->sum('total_bayar');

                $keluar = Pengeluaran::whereHas('posBiaya', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->sum('jumlah');

                $bulanLabels->push($label);
                $dataPenerimaan->push((int) $pemasukan);
                $dataPengeluaran->push((int) $keluar);
            }
        }

        return compact('bulanLabels', 'dataPenerimaan', 'dataPengeluaran');
    }

    public function getTransaksiTerbaru(?TahunAjaran $tahunAktif): Collection
    {
        if (!$tahunAktif) return collect();

        return Transaksi::with(['siswaTahunAjaran.siswa', 'user'])
            ->whereHas('siswaTahunAjaran', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(5)
            ->get();
    }

    /**
     * Hitung jumlah siswa yang belum melunasi SPP bulan-bulan sebelumnya (terlewat).
     */
    public function getSppTerlewatBelumLunas(?TahunAjaran $tahunAktif): int
    {
        if (!$tahunAktif) return 0;

        return TagihanSpp::whereHas('siswaTahunAjaran', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->whereIn('status', [TagihanSpp::STATUS_BELUM, TagihanSpp::STATUS_CICILAN])
            ->where(function ($q) {
                $q->where('tahun', '<', now()->year)
                  ->orWhere(function ($sub) {
                      $sub->where('tahun', now()->year)
                          ->where('bulan', '<', now()->month);
                  });
            })
            ->distinct('siswa_tahun_ajaran_id')
            ->count('siswa_tahun_ajaran_id');
    }
}
