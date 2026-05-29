<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreSiswaTahunAjaranRequest;
use App\Models\Siswa;
use App\Models\SiswaTahunAjaran;
use App\Models\TahunAjaran;
use App\Services\TagihanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SiswaTahunAjaranController extends Controller
{
    public function __construct(
        private readonly TagihanService $tagihanService,
    ) {}

    /**
     * Daftar siswa dan status aktivasi mereka di tahun ajaran aktif.
     */
    public function index(): View
    {
        $tahunAktif = TahunAjaran::aktif();

        $siswaList = Siswa::with([
            'tahunAjaran' => function ($query) use ($tahunAktif) {
                $query->where('tahun_ajaran_id', $tahunAktif?->id);
            },
        ])->orderBy('nama')->paginate(20);

        $semua = TahunAjaran::orderByDesc('nama')->get();

        return view('master.siswa-tahun-ajaran.index', compact(
            'siswaList',
            'tahunAktif',
            'semua',
        ));
    }

    /**
     * Aktifkan siswa ke tahun ajaran (generate 12 tagihan SPP otomatis).
     */
    public function store(StoreSiswaTahunAjaranRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Cegah duplikasi: satu siswa hanya boleh aktif sekali per tahun ajaran
        $sudahAda = SiswaTahunAjaran::where('siswa_id', $data['siswa_id'])
            ->where('tahun_ajaran_id', $data['tahun_ajaran_id'])
            ->exists();

        if ($sudahAda) {
            return back()->withErrors([
                'error' => 'Siswa sudah diaktifkan di tahun ajaran ini.',
            ]);
        }

        $sta = SiswaTahunAjaran::create([
            'siswa_id'        => $data['siswa_id'],
            'tahun_ajaran_id' => $data['tahun_ajaran_id'],
            'tarif_spp'       => $data['tarif_spp'],
            'tunggakan_awal'  => $data['tunggakan_awal'] ?? 0,
        ]);

        // Load relasi tahunAjaran agar TagihanService bisa ambil nama tahun
        $sta->load('tahunAjaran');

        // Generate 12 tagihan SPP otomatis
        $this->tagihanService->generateSpp($sta);

        $siswa = Siswa::find($data['siswa_id']);

        return redirect()->route('master.siswa-tahun-ajaran.index')
            ->with('sukses', "Siswa {$siswa->nama} berhasil diaktifkan. 12 tagihan SPP telah dibuat.");
    }
}
