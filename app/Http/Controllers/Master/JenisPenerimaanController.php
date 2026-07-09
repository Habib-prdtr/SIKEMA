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
use Illuminate\Http\Request;
use Illuminate\View\View;

class JenisPenerimaanController extends Controller
{
    public function __construct(
        private readonly TagihanService $tagihanService,
        private readonly MasterDataService $masterDataService
    ) {}

    /**
     * Daftar jenis penerimaan untuk tahun ajaran aktif atau terpilih.
     */
    public function index(Request $request): View
    {
        $tahunList = TahunAjaran::orderByDesc('nama')->get();
        $tahunAktif = TahunAjaran::aktif();

        $selectedTahunId = $request->get('tahun_ajaran_id', $tahunAktif?->id);
        $tahunFilter = $selectedTahunId ? TahunAjaran::find($selectedTahunId) : $tahunAktif;

        $jenisPenerimaan = $this->masterDataService->getJenisPenerimaan($tahunFilter);

        return view('master.jenis-penerimaan.index', compact(
            'jenisPenerimaan',
            'tahunAktif',
            'tahunList',
            'selectedTahunId',
            'tahunFilter'
        ));
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

    /**
     * Tampilkan rincian siswa yang sudah membayar jenis penerimaan ini.
     */
    public function pembayar(JenisPenerimaan $jenisPenerimaan): View
    {
        $tagihanList = $jenisPenerimaan->tagihanIuran()
            ->with(['siswaTahunAjaran.siswa'])
            ->where('terbayar', '>', 0)
            ->get();

        $totalTerkumpul = $tagihanList->sum('terbayar');

        // Statistik pendukung
        $totalSiswa = $jenisPenerimaan->tagihanIuran()->count();
        $lunasCount = $jenisPenerimaan->tagihanIuran()->where('status', 'lunas')->count();
        $belumCount = $jenisPenerimaan->tagihanIuran()->where('status', '!=', 'lunas')->count();

        return view('master.jenis-penerimaan.pembayar', compact(
            'jenisPenerimaan',
            'tagihanList',
            'totalTerkumpul',
            'totalSiswa',
            'lunasCount',
            'belumCount'
        ));
    }
}
