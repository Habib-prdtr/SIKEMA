<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
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
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'kelas' => 'required|string|max:50',
            'tarif' => 'required|integer|min:0',
        ], [
            'kelas.required' => 'Nama/Tingkat Kelas wajib diisi.',
            'tarif.required' => 'Tarif wajib diisi.',
            'tarif.integer' => 'Tarif harus berupa angka.',
            'tarif.min' => 'Tarif tidak boleh negatif.',
        ]);

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
    public function update(Request $request, MasterTarifSpp $tarifSpp): RedirectResponse
    {
        $data = $request->validate([
            'kelas' => 'required|string|max:50',
            'tarif' => 'required|integer|min:0',
        ], [
            'kelas.required' => 'Nama/Tingkat Kelas wajib diisi.',
            'tarif.required' => 'Tarif wajib diisi.',
            'tarif.integer' => 'Tarif harus berupa angka.',
            'tarif.min' => 'Tarif tidak boleh negatif.',
        ]);

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
