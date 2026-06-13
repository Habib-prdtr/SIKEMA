<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreAllSiswaTahunAjaranRequest;
use App\Http\Requests\Master\StoreSiswaTahunAjaranRequest;
use App\Http\Requests\Master\UpdateSiswaTahunAjaranSppRequest;
use App\Models\Siswa;
use App\Models\SiswaTahunAjaran;
use App\Models\TahunAjaran;
use App\Services\MasterDataService;
use App\Services\TagihanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SiswaTahunAjaranController extends Controller
{
    public function __construct(
        private readonly TagihanService $tagihanService,
        private readonly MasterDataService $masterDataService
    ) {}

    /**
     * Daftar siswa dan status aktivasi mereka di tahun ajaran aktif.
     */
    public function index(): View
    {
        $tahunAktif = TahunAjaran::aktif();

        $siswaList = $this->masterDataService->getSiswaTahunAjaranList($tahunAktif);
        $semua = $this->masterDataService->getTahunList();
        
        // Fetch distinct classes
        $daftarKelas = \App\Models\Siswa::distinct()->orderBy('kelas')->pluck('kelas')->filter();

        $tarifSppList = collect();
        if ($tahunAktif) {
            $tarifSppList = \App\Models\MasterTarifSpp::where('tahun_ajaran_id', $tahunAktif->id)
                ->orderBy('kelas')
                ->get();
        }

        return view('master.siswa-tahun-ajaran.index', compact(
            'siswaList',
            'tahunAktif',
            'semua',
            'tarifSppList',
            'daftarKelas',
        ));
    }

    /**
     * Aktifkan siswa ke tahun ajaran (generate 12 tagihan SPP otomatis).
     */
    public function store(StoreSiswaTahunAjaranRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Cegah duplikasi: satu siswa hanya boleh aktif sekali per tahun ajaran
        if ($this->masterDataService->cekSiswaSudahAktifTahunIni($data['siswa_id'], $data['tahun_ajaran_id'])) {
            return back()->withErrors([
                'error' => 'Siswa sudah diaktifkan di tahun ajaran ini.',
            ]);
        }

        $masterTarif = \App\Models\MasterTarifSpp::findOrFail($data['master_tarif_spp_id']);
        $tarifSpp = $masterTarif->tarif;

        DB::beginTransaction();
        try {
            $sta = SiswaTahunAjaran::create([
                'siswa_id' => $data['siswa_id'],
                'tahun_ajaran_id' => $data['tahun_ajaran_id'],
                'tarif_spp' => $tarifSpp,
                'tunggakan_awal' => $data['tunggakan_awal'] ?? 0,
            ]);

            // Load relasi tahunAjaran agar TagihanService bisa ambil nama tahun
            $sta->load('tahunAjaran');

            // Generate 12 tagihan SPP otomatis
            $this->tagihanService->generateSpp($sta);

            // Generate tagihan iuran aktif otomatis
            $this->tagihanService->generateIuranUntukSiswa($sta);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal mengaktifkan siswa: ' . $e->getMessage()]);
        }

        $siswa = Siswa::find($data['siswa_id']);

        return redirect()->route('master.siswa-tahun-ajaran.index')
            ->with('sukses', "Siswa {$siswa->nama} berhasil diaktifkan. 12 tagihan SPP telah dibuat.");
    }

    /**
     * Aktifkan seluruh siswa aktif ke tahun ajaran.
     */
    public function storeAll(StoreAllSiswaTahunAjaranRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $tahunAjaranId = $data['tahun_ajaran_id'];
        $kelas = $data['kelas'];
        $masterTarif = \App\Models\MasterTarifSpp::findOrFail($data['master_tarif_spp_id']);
        $tarifSpp = $masterTarif->tarif;

        $semuaSiswaAktifDiKelas = Siswa::where('status', Siswa::STATUS_AKTIF)
            ->where('kelas', $kelas)
            ->get();

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($semuaSiswaAktifDiKelas as $siswa) {
                // Skip if already activated
                if ($this->masterDataService->cekSiswaSudahAktifTahunIni($siswa->id, $tahunAjaranId)) {
                    continue;
                }

                $sta = SiswaTahunAjaran::create([
                    'siswa_id' => $siswa->id,
                    'tahun_ajaran_id' => $tahunAjaranId,
                    'tarif_spp' => $tarifSpp,
                    'tunggakan_awal' => 0,
                ]);

                $sta->load('tahunAjaran');
                $this->tagihanService->generateSpp($sta);
                $this->tagihanService->generateIuranUntukSiswa($sta);
                $count++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal mengaktifkan siswa: ' . $e->getMessage()]);
        }

        return redirect()->route('master.siswa-tahun-ajaran.index')
            ->with('sukses', "Berhasil mengaktifkan {$count} siswa aktif di kelas {$kelas}.");
    }

    /**
     * Update tarif SPP siswa di tahun ajaran aktif.
     */
    public function updateSpp(UpdateSiswaTahunAjaranSppRequest $request, SiswaTahunAjaran $siswaTahunAjaran): RedirectResponse
    {
        $data = $request->validated();

        $masterTarif = \App\Models\MasterTarifSpp::findOrFail($data['master_tarif_spp_id']);
        $tarifSpp = $masterTarif->tarif;

        DB::beginTransaction();
        try {
            // Update tarif SPP di record siswa_tahun_ajaran
            $siswaTahunAjaran->update([
                'tarif_spp' => $tarifSpp,
            ]);

            // Update tagihan SPP yang statusnya masih 'belum'
            $siswaTahunAjaran->tagihanSpp()
                ->where('status', 'belum')
                ->update([
                    'tagihan' => $tarifSpp,
                ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memperbarui tarif SPP: ' . $e->getMessage()]);
        }

        return redirect()->route('master.siswa-tahun-ajaran.index')
            ->with('sukses', "Tarif SPP untuk siswa {$siswaTahunAjaran->siswa->nama} berhasil diperbarui.");
    }
}
