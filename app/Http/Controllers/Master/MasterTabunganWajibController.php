<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreTabunganWajibRequest;
use App\Http\Requests\Master\UpdateTabunganWajibRequest;
use App\Models\MasterTabunganWajib;
use App\Models\TahunAjaran;
use App\Services\MasterDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MasterTabunganWajibController extends Controller
{
    public function __construct(
        private readonly MasterDataService $masterDataService
    ) {}

    /**
     * Tampilkan daftar tarif Tabungan Wajib.
     */
    public function index(): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $tabunganWajib = collect();
        $daftarTahunAjaran = TahunAjaran::where('id', '!=', $tahunAktif?->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $tahunSebelumnya = $daftarTahunAjaran->first();
        $tabunganWajibSebelumnya = collect();

        if ($tahunSebelumnya) {
            $tabunganWajibSebelumnya = MasterTabunganWajib::where('tahun_ajaran_id', $tahunSebelumnya->id)
                ->orderBy('kelas')
                ->get();
        }

        if ($tahunAktif) {
            $tabunganWajib = MasterTabunganWajib::where('tahun_ajaran_id', $tahunAktif->id)
                ->orderBy('kelas')
                ->get();
        }

        return view('master.tabungan-wajib.index', compact(
            'tabunganWajib',
            'tahunAktif',
            'daftarTahunAjaran',
            'tahunSebelumnya',
            'tabunganWajibSebelumnya'
        ));
    }

    /**
     * Extract tarif Tabungan Wajib dari tahun ajaran sebelumnya.
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

        $tarifLama = MasterTabunganWajib::where('tahun_ajaran_id', $tahunSebelumnya->id)->get();

        if ($tarifLama->isEmpty()) {
            return back()->withErrors(['error' => 'Tidak ada tarif Tabungan Wajib pada tahun ajaran sebelumnya.']);
        }

        foreach ($tarifLama as $tarif) {
            MasterTabunganWajib::updateOrCreate(
                [
                    'tahun_ajaran_id' => $tahunAktif->id,
                    'kelas' => $tarif->kelas,
                ],
                [
                    'tarif' => $tarif->tarif,
                ]
            );
        }

        return redirect()->route('master.tabungan-wajib.index')
            ->with('sukses', "Tarif Tabungan Wajib berhasil diekstrak dari tahun ajaran {$tahunSebelumnya->nama}.");
    }

    /**
     * Simpan tarif Tabungan Wajib baru.
     */
    public function store(StoreTabunganWajibRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Cegah duplikasi kelas di tahun ajaran yang sama
        if ($this->masterDataService->cekTabunganWajibExists($data['tahun_ajaran_id'], $data['kelas'])) {
            return back()->withErrors(['error' => 'Tarif Tabungan Wajib untuk kelas tersebut sudah didefinisikan pada tahun ajaran ini.']);
        }

        $this->masterDataService->simpanTabunganWajib($data);

        return redirect()->route('master.tabungan-wajib.index')
            ->with('sukses', "Tarif Tabungan Wajib kelas '{$data['kelas']}' berhasil ditambahkan.");
    }

    /**
     * Update tarif Tabungan Wajib.
     */
    public function update(UpdateTabunganWajibRequest $request, MasterTabunganWajib $tabunganWajib): RedirectResponse
    {
        $data = $request->validated();

        // Cegah duplikasi kelas (abaikan diri sendiri)
        if ($this->masterDataService->cekTabunganWajibExists($tabunganWajib->tahun_ajaran_id, $data['kelas'], $tabunganWajib->id)) {
            return back()->withErrors(['error' => 'Tarif Tabungan Wajib untuk kelas tersebut sudah didefinisikan pada tahun ajaran ini.']);
        }

        $this->masterDataService->updateTabunganWajib($tabunganWajib, $data);

        return redirect()->route('master.tabungan-wajib.index')
            ->with('sukses', "Tarif Tabungan Wajib kelas '{$data['kelas']}' berhasil diperbarui.");
    }

    /**
     * Hapus tarif Tabungan Wajib.
     */
    public function destroy(MasterTabunganWajib $tabunganWajib): RedirectResponse
    {
        $kelas = $tabunganWajib->kelas;
        $this->masterDataService->hapusTabunganWajib($tabunganWajib);

        return redirect()->route('master.tabungan-wajib.index')
            ->with('sukses', "Tarif Tabungan Wajib kelas '{$kelas}' berhasil dihapus.");
    }
}
