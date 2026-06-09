<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreTarifSppRequest;
use App\Http\Requests\Master\UpdateTarifSppRequest;
use App\Models\MasterTarifSpp;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterTarifSppController extends Controller
{
    /**
     * Tampilkan daftar tarif SPP.
     */
    public function index(): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $tarifSpp = collect();

        if ($tahunAktif) {
            $tarifSpp = MasterTarifSpp::where('tahun_ajaran_id', $tahunAktif->id)
                ->orderBy('kelas')
                ->get();
        }

        return view('master.tarif-spp.index', compact('tarifSpp', 'tahunAktif'));
    }

    /**
     * Simpan tarif SPP baru.
     */
    public function store(StoreTarifSppRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Cegah duplikasi kelas di tahun ajaran yang sama
        $exists = MasterTarifSpp::where('tahun_ajaran_id', $data['tahun_ajaran_id'])
            ->where('kelas', $data['kelas'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'Tarif SPP untuk kelas tersebut sudah didefinisikan pada tahun ajaran ini.']);
        }

        MasterTarifSpp::create($data);

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
        $exists = MasterTarifSpp::where('tahun_ajaran_id', $tarifSpp->tahun_ajaran_id)
            ->where('kelas', $data['kelas'])
            ->where('id', '!=', $tarifSpp->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['error' => 'Tarif SPP untuk kelas tersebut sudah didefinisikan pada tahun ajaran ini.']);
        }

        $tarifSpp->update($data);

        return redirect()->route('master.tarif-spp.index')
            ->with('sukses', "Tarif SPP kelas '{$data['kelas']}' berhasil diperbarui.");
    }

    /**
     * Hapus tarif SPP.
     */
    public function destroy(MasterTarifSpp $tarifSpp): RedirectResponse
    {
        $kelas = $tarifSpp->kelas;
        $tarifSpp->delete();

        return redirect()->route('master.tarif-spp.index')
            ->with('sukses', "Tarif SPP kelas '{$kelas}' berhasil dihapus.");
    }
}
