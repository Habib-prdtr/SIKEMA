<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreSiswaRequest;
use App\Http\Requests\Master\UpdateSiswaRequest;
use App\Models\Siswa;
use App\Services\MasterDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DataSiswaController extends Controller
{
    protected MasterDataService $masterDataService;

    public function __construct(MasterDataService $masterDataService)
    {
        $this->masterDataService = $masterDataService;
    }

    /**
     * Daftar siswa dengan pagination dan pencarian.
     */
    public function index(Request $request): View
    {
        $siswa = $this->masterDataService->getDaftarSiswa($request);

        return view('master.siswa.index', compact('siswa'));
    }

    /**
     * Form tambah siswa.
     */
    public function create(): View
    {
        return view('master.siswa.create');
    }

    /**
     * Simpan siswa baru.
     */
    public function store(StoreSiswaRequest $request): RedirectResponse
    {
        Siswa::create($request->validated());

        return redirect()->route('master.siswa.index')
            ->with('sukses', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Form edit siswa.
     */
    public function edit(Siswa $siswa): View
    {
        return view('master.siswa.edit', compact('siswa'));
    }

    /**
     * Update data siswa.
     */
    public function update(UpdateSiswaRequest $request, Siswa $siswa): RedirectResponse
    {
        $siswa->update($request->validated());

        return redirect()->route('master.siswa.index')
            ->with('sukses', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Hapus siswa (soft-check: cegah hapus jika punya transaksi).
     */
    public function destroy(Siswa $siswa): RedirectResponse
    {
        if ($this->masterDataService->cekSiswaPunyaTransaksi($siswa)) {
            return back()->withErrors([
                'error' => 'Siswa tidak dapat dihapus karena sudah memiliki data transaksi.',
            ]);
        }

        // Cascade delete: siswa_tahun_ajaran → tagihan_spp/iuran akan ikut terhapus
        $siswa->delete();

        return redirect()->route('master.siswa.index')
            ->with('sukses', 'Data siswa berhasil dihapus.');
    }
}
