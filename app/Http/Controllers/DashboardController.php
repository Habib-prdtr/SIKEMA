<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Siswa;
use App\Models\SiswaTahunAjaran;
use App\Models\TagihanSpp;
use App\Models\TahunAjaran;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tahunAktif = TahunAjaran::aktif();

        // ── Statistik utama ────────────────────────────────────
        $jumlahSiswa = SiswaTahunAjaran::where('tahun_ajaran_id', $tahunAktif?->id)->count();

        $totalPenerimaanBulanIni = Transaksi::whereHas('siswaTahunAjaran', fn ($q) =>
            $q->where('tahun_ajaran_id', $tahunAktif?->id)
        )
        ->whereMonth('tanggal', now()->month)
        ->whereYear('tanggal', now()->year)
        ->sum('total_bayar');

        $totalPengeluaranBulanIni = Pengeluaran::whereHas('posBiaya', fn ($q) =>
            $q->where('tahun_ajaran_id', $tahunAktif?->id)
        )
        ->whereMonth('tanggal', now()->month)
        ->whereYear('tanggal', now()->year)
        ->sum('jumlah');

        // ── Tunggakan ──────────────────────────────────────────
        // Hanya ambil siswa yang punya tunggakan_awal > 0, lalu hitung sisa tunggakannya
        $siswaDenganTunggakan = SiswaTahunAjaran::where('tahun_ajaran_id', $tahunAktif?->id)
            ->where('tunggakan_awal', '>', 0)
            ->with('transaksi.details')
            ->get();
        
        $tunggakanService = app(\App\Services\TunggakanService::class);
        $siswaAdaTunggakan = 0;
        $totalTunggakanAwal = 0; // Sebenarnya ini adalah "Total Sisa Tunggakan", tapi variabel di view namanya totalTunggakanAwal

        foreach ($siswaDenganTunggakan as $sta) {
            $sisa = $tunggakanService->hitungSisa($sta);
            if ($sisa > 0) {
                $siswaAdaTunggakan++;
                $totalTunggakanAwal += $sisa;
            }
        }

        // ── SPP belum lunas bulan ini ──────────────────────────
        $sppBelumLunas = TagihanSpp::whereHas('siswaTahunAjaran', fn ($q) =>
            $q->where('tahun_ajaran_id', $tahunAktif?->id)
        )
        ->where('bulan', now()->month)
        ->where('tahun', now()->year)
        ->whereIn('status', ['belum', 'cicilan'])
        ->count();

        // ── Saldo kas: saldo_awal + semua penerimaan - semua pengeluaran ──
        $saldoAwal = $tahunAktif?->saldoAwal?->jumlah ?? 0;
        $totalPenerimaan = Transaksi::whereHas('siswaTahunAjaran', fn ($q) =>
            $q->where('tahun_ajaran_id', $tahunAktif?->id)
        )->sum('total_bayar');
        $totalPengeluaran = Pengeluaran::whereHas('posBiaya', fn ($q) =>
            $q->where('tahun_ajaran_id', $tahunAktif?->id)
        )->sum('jumlah');
        $totalSaldo = $saldoAwal + $totalPenerimaan - $totalPengeluaran;

        // ── Grafik bulanan (6 bulan terakhir) ─────────────────
        $bulanLabels = collect();
        $dataPenerimaan = collect();
        $dataPengeluaran = collect();

        for ($i = 5; $i >= 0; $i--) {
            $bulan  = now()->subMonths($i)->month;
            $tahun  = now()->subMonths($i)->year;
            $label  = now()->subMonths($i)->locale('id')->isoFormat('MMM');

            $pemasukan = Transaksi::whereHas('siswaTahunAjaran', fn ($q) =>
                $q->where('tahun_ajaran_id', $tahunAktif?->id)
            )->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->sum('total_bayar');

            $keluar = Pengeluaran::whereHas('posBiaya', fn ($q) =>
                $q->where('tahun_ajaran_id', $tahunAktif?->id)
            )->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)->sum('jumlah');

            $bulanLabels->push($label);
            $dataPenerimaan->push((int) $pemasukan);
            $dataPengeluaran->push((int) $keluar);
        }

        // ── Transaksi terbaru ──────────────────────────────────
        $transaksiTerbaru = Transaksi::with(['siswaTahunAjaran.siswa', 'user'])
            ->whereHas('siswaTahunAjaran', fn ($q) =>
                $q->where('tahun_ajaran_id', $tahunAktif?->id)
            )
            ->orderByDesc('tanggal')->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'tahunAktif',
            'jumlahSiswa',
            'totalPenerimaanBulanIni',
            'totalPengeluaranBulanIni',
            'siswaAdaTunggakan',
            'totalTunggakanAwal',
            'sppBelumLunas',
            'totalSaldo',
            'bulanLabels',
            'dataPenerimaan',
            'dataPengeluaran',
            'transaksiTerbaru',
        ));
    }
}
