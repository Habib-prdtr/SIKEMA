<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\SimpanPenerimaanRequest;
use App\Models\Sekolah;
use App\Models\TahunAjaran;
use App\Models\Transaksi;
use App\Services\TransaksiService;
use App\Services\TunggakanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenerimaanController extends Controller
{
    public function __construct(
        private readonly TransaksiService $transaksiService,
        private readonly TunggakanService $tunggakanService,
    ) {}

    /**
     * Daftar transaksi penerimaan (riwayat).
     */
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $transaksi = $this->transaksiService->getDaftarPenerimaan($request, $tahunAktif);

        return view('transaksi.penerimaan.index', compact('transaksi', 'tahunAktif'));
    }

    /**
     * Form catat penerimaan: cari siswa, tampilkan tagihan aktif.
     */
    public function create(Request $request): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $siswa = null;
        $tagihanSpp = collect();
        $tagihanIuran = collect();
        $sisaTunggakan = 0;

        if ($request->filled('no_induk')) {
            $sta = $this->transaksiService->getSiswaUntukTransaksi($request->no_induk, $tahunAktif);

            if ($sta) {
                $siswa = $sta;
                $tagihanSpp = $sta->tagihanSpp;
                $tagihanIuran = $sta->tagihanIuran;
                $sisaTunggakan = $this->tunggakanService->hitungSisa($sta);
            }
        }

        return view('transaksi.penerimaan.create', compact(
            'tahunAktif',
            'siswa',
            'tagihanSpp',
            'tagihanIuran',
            'sisaTunggakan',
        ));
    }

    /**
     * Simpan transaksi penerimaan (bulk payment).
     */
    public function store(SimpanPenerimaanRequest $request): RedirectResponse
    {
        $transaksi = $this->transaksiService->simpanPenerimaan(
            $request->validated(),
            $request->user(),
        );

        return redirect()->route('penerimaan.show', $transaksi)
            ->with('sukses', "Transaksi {$transaksi->no_transaksi} berhasil disimpan.");
    }

    /**
     * Detail transaksi + opsi cetak kwitansi.
     */
    public function show(Transaksi $transaksi): View
    {
        $transaksi->load([
            'siswaTahunAjaran.siswa',
            'siswaTahunAjaran.tahunAjaran',
            'details.jenisPenerimaan',
            'user',
        ]);

        $sekolah = Sekolah::getData();

        return view('transaksi.penerimaan.show', compact('transaksi', 'sekolah'));
    }
}
