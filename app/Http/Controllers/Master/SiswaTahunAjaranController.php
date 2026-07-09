<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreAllSiswaTahunAjaranRequest;
use App\Http\Requests\Master\StoreSiswaTahunAjaranRequest;
use App\Http\Requests\Master\UpdateSiswaTahunAjaranSppRequest;
use App\Http\Requests\Master\UpdateSiswaTahunAjaranTunggakanRequest;
use App\Models\Siswa;
use App\Models\SiswaTahunAjaran;
use App\Models\TahunAjaran;
use App\Services\MasterDataService;
use App\Services\TagihanService;
use App\Services\TunggakanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SiswaTahunAjaranController extends Controller
{
    public function __construct(
        private readonly TagihanService $tagihanService,
        private readonly MasterDataService $masterDataService,
        private readonly TunggakanService $tunggakanService
    ) {}

    /**
     * Daftar siswa dan status aktivasi mereka di tahun ajaran aktif.
     */
    public function index(Request $request): View
    {
        $tahunAktif = TahunAjaran::aktif();

        $siswaList = $this->masterDataService->getSiswaTahunAjaranList(
            $tahunAktif,
            $request->query('cari'),
            $request->query('kelas')
        );
        $semua = $this->masterDataService->getTahunList();

        // Fetch distinct classes
        $daftarKelas = $this->masterDataService->getDaftarKelasSiswa();

        $tarifSppList = $this->masterDataService->getTarifSpp($tahunAktif);
        $dispensasiList = \App\Models\Dispensasi::orderBy('nama')->get();

        return view('master.siswa-tahun-ajaran.index', compact(
            'siswaList',
            'tahunAktif',
            'semua',
            'tarifSppList',
            'daftarKelas',
            'dispensasiList',
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

        try {
            $siswa = $this->masterDataService->aktifkanSiswa($data, $this->tagihanService);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengaktifkan siswa: ' . $e->getMessage()]);
        }

        return redirect()->route('master.siswa-tahun-ajaran.index')
            ->with('sukses', "Siswa {$siswa->nama} berhasil diaktifkan. 12 tagihan SPP telah dibuat.");
    }

    /**
     * Aktifkan seluruh siswa aktif ke tahun ajaran.
     */
    public function storeAll(StoreAllSiswaTahunAjaranRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $kelas = $data['kelas'];
        $tahunAjaranId = $data['tahun_ajaran_id'];
        $masterTarif = \App\Models\MasterTarifSpp::findOrFail($data['master_tarif_spp_id']);
        $tarifSpp = $masterTarif->tarif;

        $semuaSiswaAktifDiKelas = Siswa::where('status', Siswa::STATUS_AKTIF)
            ->where('kelas', $kelas)
            ->get();

        try {
            DB::beginTransaction();
            $count = 0;
            foreach ($semuaSiswaAktifDiKelas as $siswa) {
                // Skip if already activated
                if ($this->masterDataService->cekSiswaSudahAktifTahunIni($siswa->id, $tahunAjaranId)) {
                    continue;
                }

                // Cari data tahun ajaran sebelumnya untuk hitung sisa tunggakan
                // Mencari tahun ajaran dengan ID lebih kecil dari ID saat ini
                $previousSta = SiswaTahunAjaran::where('siswa_id', $siswa->id)
                    ->whereHas('tahunAjaran', function ($query) use ($tahunAjaranId) {
                        $query->where('id', '<', $tahunAjaranId);
                    })
                    ->with('transaksi.details')
                    ->latest()
                    ->first();

                $tunggakanAwal = 0;
                if ($previousSta) {
                    $tunggakanAwal = $this->tunggakanService->hitungSisa($previousSta);
                }

                $sta = SiswaTahunAjaran::create([
                    'siswa_id' => $siswa->id,
                    'tahun_ajaran_id' => $tahunAjaranId,
                    'tarif_spp' => $tarifSpp,
                    'tunggakan_awal' => $tunggakanAwal,
                ]);

                $sta->load('tahunAjaran');
                $this->tagihanService->generateSpp($sta);
                $this->tagihanService->generateIuranUntukSiswa($sta);
                $count++;
            }
            DB::commit();
        } catch (\Exception $e) {
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
        try {
            $this->masterDataService->updateSppSiswa($siswaTahunAjaran, $request->validated());
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui tarif SPP: ' . $e->getMessage()]);
        }

        return redirect()->route('master.siswa-tahun-ajaran.index')
            ->with('sukses', "Tarif SPP untuk siswa {$siswaTahunAjaran->siswa->nama} berhasil diperbarui.");
    }

    /**
     * Update tunggakan awal siswa.
     */
    public function updateTunggakanAwal(UpdateSiswaTahunAjaranTunggakanRequest $request, SiswaTahunAjaran $siswaTahunAjaran): RedirectResponse
    {
        $data = $request->validated();

        $siswaTahunAjaran->update([
            'tunggakan_awal' => $data['tunggakan_awal'],
        ]);

        return redirect()->route('master.siswa-tahun-ajaran.index')
            ->with('sukses', "Tunggakan awal siswa {$siswaTahunAjaran->siswa->nama} berhasil diperbarui.");
    }
}
