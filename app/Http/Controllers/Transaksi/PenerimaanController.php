<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\SimpanPenerimaanRequest;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\SiswaTahunAjaran;
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

        $query = Transaksi::with(['siswaTahunAjaran.siswa', 'user'])
            ->whereHas('siswaTahunAjaran', function ($q) use ($tahunAktif) {
                $q->where('tahun_ajaran_id', $tahunAktif?->id);
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        // Filter pencarian — dibatasi dalam scope whereHas agar tidak bocor ke tahun ajaran lain
        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($outer) use ($cari) {
                $outer->whereHas('siswaTahunAjaran.siswa', function ($q) use ($cari) {
                    $q->where('nama', 'like', "%{$cari}%")
                        ->orWhere('no_induk', 'like', "%{$cari}%");
                })->orWhere('no_transaksi', 'like', "%{$cari}%");
            });
        }

        $transaksi = $query->paginate(20)->withQueryString();

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
            $siswaCari = Siswa::where('no_induk', $request->no_induk)->first();

            if ($siswaCari) {
                $sta = SiswaTahunAjaran::with([
                    'siswa',
                    'tahunAjaran',
                    'tagihanSpp' => fn ($q) => $q->orderBy('tahun')->orderBy('bulan'),
                    'tagihanIuran' => fn ($q) => $q->with('jenisPenerimaan'),
                ])
                    ->where('siswa_id', $siswaCari->id)
                    ->where('tahun_ajaran_id', $tahunAktif?->id)
                    ->first();

                if ($sta) {
                    $siswa = $sta;
                    $tagihanSpp = $sta->tagihanSpp;
                    $tagihanIuran = $sta->tagihanIuran;
                    $sisaTunggakan = $this->tunggakanService->hitungSisa($sta);
                }
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
