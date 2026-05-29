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

        // Rekap per pos biaya
        $rekapPerPos = $pengeluaran->groupBy('pos_biaya_id')->map(function ($items) {
            return [
                'nama'  => $items->first()->posBiaya->nama,
                'total' => $items->sum('jumlah'),
            ];
        })->values();

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
