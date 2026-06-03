<?php

namespace App\Services;

use App\Models\Pengeluaran;
use App\Models\PosBiaya;
use App\Models\TahunAjaran;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class LaporanService
{
    public function getLaporanPenerimaan(Request $request, ?TahunAjaran $tahunAktif): array
    {
        $tahunList = TahunAjaran::orderByDesc('nama')->get();
        $tahunAjaranId = $request->get('tahun_ajaran_id', $tahunAktif?->id);

        $query = Transaksi::with([
            'siswaTahunAjaran.siswa',
            'details.jenisPenerimaan',
            'user',
        ])
            ->whereHas('siswaTahunAjaran', fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->orderByDesc('tanggal');

        // Filter bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        // Filter tahun kalender
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        // Filter siswa
        if ($request->filled('siswa_id')) {
            $query->whereHas('siswaTahunAjaran', fn ($q) => $q->where('siswa_id', $request->siswa_id));
        }

        $transaksi = $query->get();

        // Rekap total
        $totalPenerimaan = $transaksi->sum('total_bayar');

        // Rekap per jenis
        $totalSpp = $transaksi->flatMap(fn ($t) => $t->details)->where('jenis', 'spp')->sum('nominal');
        $totalIuran = $transaksi->flatMap(fn ($t) => $t->details)->where('jenis', 'iuran')->sum('nominal');
        $totalTunggakan = $transaksi->flatMap(fn ($t) => $t->details)->where('jenis', 'tunggakan')->sum('nominal');

        return compact(
            'transaksi',
            'tahunList',
            'tahunAktif',
            'totalPenerimaan',
            'totalSpp',
            'totalIuran',
            'totalTunggakan'
        );
    }

    public function getLaporanPengeluaran(Request $request, ?TahunAjaran $tahunAktif): array
    {
        $tahunList = TahunAjaran::orderByDesc('nama')->get();
        $tahunAjaranId = $request->get('tahun_ajaran_id', $tahunAktif?->id);

        $posList = PosBiaya::where('tahun_ajaran_id', $tahunAjaranId)
            ->orderBy('nama')
            ->get();

        $query = Pengeluaran::with(['posBiaya', 'user'])
            ->whereHas('posBiaya', fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->orderByDesc('tanggal');

        // Filter bulan
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        // Filter pos biaya
        if ($request->filled('pos_biaya_id')) {
            $query->where('pos_biaya_id', $request->pos_biaya_id);
        }

        $pengeluaran = $query->get();
        $totalPengeluaran = (int) $pengeluaran->sum('jumlah');

        // Rekap per pos biaya
        $rekapPerPos = PosBiaya::withSum('pengeluaran', 'jumlah')
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->when($request->filled('bulan'), function ($q) use ($request) {
                $q->whereHas('pengeluaran', fn ($p) => $p->where('bulan', $request->bulan));
            })
            ->orderBy('nama')
            ->get()
            ->map(function ($pos) {
                return (object) [
                    'nama' => $pos->nama,
                    'anggaran' => $pos->anggaran ?? 0,
                    'total' => (int) ($pos->pengeluaran_sum_jumlah ?? 0),
                ];
            })
            ->filter(fn ($r) => $r->total > 0);

        return compact(
            'pengeluaran',
            'tahunList',
            'posList',
            'tahunAktif',
            'totalPengeluaran',
            'rekapPerPos'
        );
    }
}

