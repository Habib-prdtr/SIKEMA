<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreJenisPenerimaanRequest;
use App\Http\Requests\Master\UpdateJenisPenerimaanRequest;
use App\Models\JenisPenerimaan;
use App\Models\TahunAjaran;
use App\Services\MasterDataService;
use App\Services\TagihanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JenisPenerimaanController extends Controller
{
    public function __construct(
        private readonly TagihanService $tagihanService,
        private readonly MasterDataService $masterDataService
    ) {}

    /**
     * Daftar jenis penerimaan untuk tahun ajaran aktif.
     */
    public function index(): View
    {
        $tahunAktif = TahunAjaran::aktif();
        $jenisPenerimaan = $this->masterDataService->getJenisPenerimaan($tahunAktif);

        return view('master.jenis-penerimaan.index', compact('jenisPenerimaan', 'tahunAktif'));
    }

    /**
     * Simpan jenis penerimaan baru.
     * Jika is_aktif = true, generate tagihan iuran untuk semua siswa aktif tahun ini.
     */
    public function store(StoreJenisPenerimaanRequest $request): RedirectResponse
    {
        $jp = $this->masterDataService->simpanJenisPenerimaan($request->validated(), $this->tagihanService);

        return redirect()->route('master.jenis-penerimaan.index')
            ->with('sukses', "Jenis penerimaan '{$jp->nama}' berhasil ditambahkan.");
    }

    /**
     * Update jenis penerimaan.
     * Jika status berubah dari nonaktif → aktif, generate tagihan yang belum ada.
     */
    public function update(UpdateJenisPenerimaanRequest $request, JenisPenerimaan $jenisPenerimaan): RedirectResponse
    {
        $this->masterDataService->updateJenisPenerimaan($jenisPenerimaan, $request->validated(), $this->tagihanService);

        return redirect()->route('master.jenis-penerimaan.index')
            ->with('sukses', "Jenis penerimaan '{$jenisPenerimaan->nama}' berhasil diperbarui.");
    }

    /**
     * Hapus jenis penerimaan (hanya jika belum ada pembayaran).
     */
    public function destroy(JenisPenerimaan $jenisPenerimaan): RedirectResponse
    {
        if ($this->masterDataService->cekJenisPenerimaanAdaPembayaran($jenisPenerimaan)) {
            return back()->withErrors([
                'error' => "Jenis penerimaan '{$jenisPenerimaan->nama}' tidak dapat dihapus karena sudah ada pembayaran.",
            ]);
        }

        $nama = $jenisPenerimaan->nama;
        $this->masterDataService->hapusJenisPenerimaan($jenisPenerimaan);

        return redirect()->route('master.jenis-penerimaan.index')
            ->with('sukses', "Jenis penerimaan '{$nama}' berhasil dihapus.");
    }
}
