<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\SimpanPengeluaranRequest;
use App\Models\Pengeluaran;
use App\Models\Sekolah;
use App\Models\TahunAjaran;
use App\Services\TransaksiService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengeluaranController extends Controller
{
    public function __construct(
        private readonly TransaksiService $transaksiService,
    ) {}

    /**
     * Daftar pengeluaran (riwayat).
     */
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::aktif();

        $pengeluaran = $this->transaksiService->getDaftarPengeluaran($request, $tahunAktif);
        $posList = $this->transaksiService->getPosBiayaAktif($tahunAktif);

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
        $posList = $this->transaksiService->getPosBiayaAktif($tahunAktif);

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

        $pengeluaran = $this->transaksiService->simpanPengeluaran($data);

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
