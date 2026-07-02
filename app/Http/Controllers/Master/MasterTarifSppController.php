<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreTarifSppRequest;
use App\Http\Requests\Master\UpdateTarifSppRequest;
use App\Models\MasterTarifSpp;
use App\Models\TahunAjaran;
use App\Services\MasterDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MasterTarifSppController extends Controller
{
    public function __construct(
        private readonly MasterDataService $masterDataService
    ) {}

    /**
     * Tampilkan daftar tarif SPP.
     */
    public function index(): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $tarifSpp = collect();
        $daftarTahunAjaran = TahunAjaran::where('id', '!=', $tahunAktif?->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $tahunSebelumnya = $daftarTahunAjaran->first();
        $tarifSppSebelumnya = collect();

        if ($tahunSebelumnya) {
            $tarifSppSebelumnya = MasterTarifSpp::where('tahun_ajaran_id', $tahunSebelumnya->id)
                ->orderBy('kelas')
                ->get();
        }

        if ($tahunAktif) {
            $tarifSpp = MasterTarifSpp::where('tahun_ajaran_id', $tahunAktif->id)
                ->orderBy('kelas')
                ->get();
        }

        return view('master.tarif-spp.index', compact('tarifSpp', 'tahunAktif', 'daftarTahunAjaran', 'tahunSebelumnya', 'tarifSppSebelumnya'));
    }

    /**
     * Extract tarif SPP dari tahun ajaran sebelumnya.
     */
    public function extract(): RedirectResponse
    {
        $tahunAktif = TahunAjaran::aktif();

        if (!$tahunAktif) {
            return back()->withErrors(['error' => 'Tidak ada tahun ajaran aktif.']);
        }

        $tahunSebelumnya = TahunAjaran::where('id', '!=', $tahunAktif->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$tahunSebelumnya) {
            return back()->withErrors(['error' => 'Tidak ada tahun ajaran sebelumnya untuk diekstrak.']);
        }

        $tarifLama = MasterTarifSpp::where('tahun_ajaran_id', $tahunSebelumnya->id)->get();

        if ($tarifLama->isEmpty()) {
            return back()->withErrors(['error' => 'Tidak ada tarif SPP pada tahun ajaran sebelumnya.']);
        }

        foreach ($tarifLama as $tarif) {
            MasterTarifSpp::updateOrCreate(
                [
                    'tahun_ajaran_id' => $tahunAktif->id,
                    'kelas' => $tarif->kelas,
                ],
                [
                    'tarif' => $tarif->tarif,
                ]
            );
        }

        return redirect()->route('master.tarif-spp.index')
            ->with('sukses', "Tarif SPP berhasil diekstrak dari tahun ajaran {$tahunSebelumnya->nama}.");
    }

    /**
     * Simpan tarif SPP baru.
     */
    public function store(StoreTarifSppRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Cegah duplikasi kelas di tahun ajaran yang sama
        if ($this->masterDataService->cekTarifSppExists($data['tahun_ajaran_id'], $data['kelas'])) {
            return back()->withErrors(['error' => 'Tarif SPP untuk kelas tersebut sudah didefinisikan pada tahun ajaran ini.']);
        }

        $this->masterDataService->simpanTarifSpp($data);

        return redirect()->route('master.tarif-spp.index')
            ->with('sukses', "Tarif SPP kelas '{$data['kelas']}' berhasil ditambahkan.");
    }

    /**
     * Update tarif SPP.
     */
    public function update(UpdateTarifSppRequest $request, MasterTarifSpp $tarifSpp): RedirectResponse
    {
        $data = $request->validated();

        // Cegah duplikasi kelas (abaikan diri sendiri)
        if ($this->masterDataService->cekTarifSppExists($tarifSpp->tahun_ajaran_id, $data['kelas'], $tarifSpp->id)) {
            return back()->withErrors(['error' => 'Tarif SPP untuk kelas tersebut sudah didefinisikan pada tahun ajaran ini.']);
        }

        $this->masterDataService->updateTarifSpp($tarifSpp, $data);

        return redirect()->route('master.tarif-spp.index')
            ->with('sukses', "Tarif SPP kelas '{$data['kelas']}' berhasil diperbarui.");
    }

    /**
     * Hapus tarif SPP.
     */
    public function destroy(MasterTarifSpp $tarifSpp): RedirectResponse
    {
        $kelas = $tarifSpp->kelas;
        $this->masterDataService->hapusTarifSpp($tarifSpp);

        return redirect()->route('master.tarif-spp.index')
            ->with('sukses', "Tarif SPP kelas '{$kelas}' berhasil dihapus.");
    }
}
