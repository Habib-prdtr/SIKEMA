<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\SimpanPengeluaranRequest;
use App\Models\Pengeluaran;
use App\Models\PosBiaya;
use App\Models\Sekolah;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengeluaranController extends Controller
{
    /**
     * Daftar pengeluaran (riwayat).
     */
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::aktif();

        $query = Pengeluaran::with(['posBiaya', 'user'])
            ->whereHas('posBiaya', function ($q) use ($tahunAktif) {
                $q->where('tahun_ajaran_id', $tahunAktif?->id);
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        // Filter bulan
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        // Filter pos biaya
        if ($request->filled('pos_biaya_id')) {
            $query->where('pos_biaya_id', $request->pos_biaya_id);
        }

        $pengeluaran = $query->paginate(20)->withQueryString();
        $posList = PosBiaya::where('tahun_ajaran_id', $tahunAktif?->id)
            ->where('is_aktif', true)
            ->orderBy('nama')
            ->get();

        return view('transaksi.pengeluaran.index', compact(
            'pengeluaran',
            'posList',
            'tahunAktif',
        ));
    }

    /**
     * Form catat pengeluaran.
     */
    public function create(): View
    {
        $tahunAktif = TahunAjaran::aktif();

        $posList = PosBiaya::where('tahun_ajaran_id', $tahunAktif?->id)
            ->where('is_aktif', true)
            ->orderBy('nama')
            ->get();

        return view('transaksi.pengeluaran.create', compact('posList', 'tahunAktif'));
    }

    /**
     * Simpan pengeluaran baru.
     */
    public function store(SimpanPengeluaranRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Isi bulan dan tahun otomatis dari tanggal
        $tanggal = Carbon::parse($data['tanggal']);
        $data['bulan'] = $tanggal->month;
        $data['tahun'] = $tanggal->year;
        $data['user_id'] = $request->user()->id;

        $pengeluaran = Pengeluaran::create($data);

        return redirect()->route('pengeluaran.show', $pengeluaran)
            ->with('sukses', 'Pengeluaran berhasil dicatat.');
    }

    /**
     * Detail pengeluaran.
     */
    public function show(Pengeluaran $pengeluaran): View
    {
        $pengeluaran->load(['posBiaya.tahunAjaran', 'user']);
        $sekolah = Sekolah::getData();

        return view('transaksi.pengeluaran.show', compact('pengeluaran', 'sekolah'));
    }
}
