<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\SiswaTahunAjaran;
use App\Models\TahunAjaran;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanPenerimaanController extends Controller
{
    /**
     * Laporan rekapitulasi penerimaan.
     *
     * Bisa difilter berdasarkan bulan, tahun ajaran, atau siswa.
     */
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $tahunList  = TahunAjaran::orderByDesc('nama')->get();

        $tahunAjaranId = $request->get('tahun_ajaran_id', $tahunAktif?->id);

        $query = Transaksi::with([
            'siswaTahunAjaran.siswa',
            'detail',
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
        $totalSpp       = $transaksi->flatMap(fn ($t) => $t->detail)->where('jenis', 'spp')->sum('nominal');
        $totalIuran     = $transaksi->flatMap(fn ($t) => $t->detail)->where('jenis', 'iuran')->sum('nominal');
        $totalTunggakan = $transaksi->flatMap(fn ($t) => $t->detail)->where('jenis', 'tunggakan')->sum('nominal');

        return view('laporan.penerimaan', compact(
            'transaksi',
            'tahunList',
            'tahunAktif',
            'totalPenerimaan',
            'totalSpp',
            'totalIuran',
            'totalTunggakan',
        ));
    }
}
