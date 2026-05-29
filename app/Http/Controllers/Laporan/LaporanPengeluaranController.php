<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Pengeluaran;
use App\Models\PosBiaya;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanPengeluaranController extends Controller
{
    /**
     * Laporan rekapitulasi pengeluaran.
     *
     * Bisa difilter berdasarkan bulan, pos biaya, atau tahun ajaran.
     */
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $tahunList  = TahunAjaran::orderByDesc('nama')->get();

        $tahunAjaranId = $request->get('tahun_ajaran_id', $tahunAktif?->id);

        // Ambil pos biaya untuk tahun ajaran yang dipilih
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

        $pengeluaran    = $query->get();
        $totalPengeluaran = (int) $pengeluaran->sum('jumlah');

        // Rekap per pos biaya — pakai withSum agar bisa akses ->nama dan ->total langsung
        $rekapPerPos = PosBiaya::withSum('pengeluaran', 'jumlah')
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->when($request->filled('bulan'), function ($q) use ($request) {
                $q->whereHas('pengeluaran', fn ($p) => $p->where('bulan', $request->bulan));
            })
            ->orderBy('nama')
            ->get()
            ->map(function ($pos) {
                return (object) [
                    'nama'     => $pos->nama,
                    'anggaran' => $pos->anggaran ?? 0,
                    'total'    => (int) ($pos->pengeluaran_sum_jumlah ?? 0),
                ];
            })
            ->filter(fn ($r) => $r->total > 0);

        return view('laporan.pengeluaran', compact(
            'pengeluaran',
            'posList',
            'tahunList',
            'tahunAktif',
            'totalPengeluaran',
            'rekapPerPos',
        ));
    }
}
