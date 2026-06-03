<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreTahunAjaranRequest;
use App\Models\TahunAjaran;
use App\Services\MasterDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TahunAjaranController extends Controller
{
    protected MasterDataService $masterDataService;

    public function __construct(MasterDataService $masterDataService)
    {
        $this->masterDataService = $masterDataService;
    }

    /**
     * Daftar semua tahun ajaran.
     */
    public function index(): View
    {
        $tahunAjaran = $this->masterDataService->getTahunList();

        return view('master.tahun-ajaran.index', compact('tahunAjaran'));
    }

    /**
     * Simpan tahun ajaran baru.
     */
    public function store(StoreTahunAjaranRequest $request): RedirectResponse
    {
        TahunAjaran::create($request->validated());

        return redirect()->route('master.tahun-ajaran.index')
            ->with('sukses', 'Tahun ajaran berhasil ditambahkan.');
    }

    /**
     * Set tahun ajaran sebagai aktif.
     * Hanya satu tahun ajaran yang boleh aktif dalam satu waktu.
     */
    public function setAktif(TahunAjaran $tahunAjaran): RedirectResponse
    {
        $tahunAjaran->setAktif();

        return redirect()->route('master.tahun-ajaran.index')
            ->with('sukses', "Tahun ajaran {$tahunAjaran->nama} kini aktif.");
    }
}
