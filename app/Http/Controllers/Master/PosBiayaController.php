<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StorePosBiayaRequest;
use App\Http\Requests\Master\UpdatePosBiayaRequest;
use App\Models\PosBiaya;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PosBiayaController extends Controller
{
    /**
     * Daftar pos biaya untuk tahun ajaran aktif.
     */
    public function index(): View
    {
        $tahunAktif = TahunAjaran::aktif();

        $posBiaya = PosBiaya::where('tahun_ajaran_id', $tahunAktif?->id)
            ->withSum('pengeluaran', 'jumlah')  // eager load sum untuk realisasi
            ->orderBy('nama')
            ->get();

        return view('master.pos-biaya.index', compact('posBiaya', 'tahunAktif'));
    }

    /**
     * Simpan pos biaya baru.
     */
    public function store(StorePosBiayaRequest $request): RedirectResponse
    {
        PosBiaya::create($request->validated());

        return redirect()->route('master.pos-biaya.index')
            ->with('sukses', 'Pos biaya berhasil ditambahkan.');
    }

    /**
     * Update pos biaya.
     */
    public function update(UpdatePosBiayaRequest $request, PosBiaya $posBiaya): RedirectResponse
    {
        $posBiaya->update($request->validated());

        return redirect()->route('master.pos-biaya.index')
            ->with('sukses', 'Pos biaya berhasil diperbarui.');
    }

    /**
     * Hapus pos biaya (hanya jika belum ada pengeluaran tercatat).
     */
    public function destroy(PosBiaya $posBiaya): RedirectResponse
    {
        if ($posBiaya->pengeluaran()->exists()) {
            return back()->withErrors([
                'error' => "Pos biaya '{$posBiaya->nama}' tidak dapat dihapus karena sudah ada pengeluaran.",
            ]);
        }

        $nama = $posBiaya->nama;
        $posBiaya->delete();

        return redirect()->route('master.pos-biaya.index')
            ->with('sukses', "Pos biaya '{$nama}' berhasil dihapus.");
    }
}
