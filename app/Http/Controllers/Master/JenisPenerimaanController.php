<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreJenisPenerimaanRequest;
use App\Http\Requests\Master\UpdateJenisPenerimaanRequest;
use App\Models\JenisPenerimaan;
use App\Models\TahunAjaran;
use App\Services\TagihanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JenisPenerimaanController extends Controller
{
    public function __construct(
        private readonly TagihanService $tagihanService,
    ) {}

    /**
     * Daftar jenis penerimaan untuk tahun ajaran aktif.
     */
    public function index(): View
    {
        $tahunAktif = TahunAjaran::aktif();

        $jenisPenerimaan = JenisPenerimaan::where('tahun_ajaran_id', $tahunAktif?->id)
            ->orderBy('urutan')
            ->get();

        return view('master.jenis-penerimaan.index', compact('jenisPenerimaan', 'tahunAktif'));
    }

    /**
     * Simpan jenis penerimaan baru.
     * Jika is_aktif = true, generate tagihan iuran untuk semua siswa aktif tahun ini.
     */
    public function store(StoreJenisPenerimaanRequest $request): RedirectResponse
    {
        $jp = JenisPenerimaan::create($request->validated());

        // Jika langsung diaktifkan, generate tagihan untuk semua siswa
        if ($jp->is_aktif) {
            $jp->load('tahunAjaran');
            $this->tagihanService->generateIuran($jp);
        }

        return redirect()->route('master.jenis-penerimaan.index')
            ->with('sukses', "Jenis penerimaan '{$jp->nama}' berhasil ditambahkan.");
    }

    /**
     * Update jenis penerimaan.
     * Jika status berubah dari nonaktif → aktif, generate tagihan yang belum ada.
     */
    public function update(UpdateJenisPenerimaanRequest $request, JenisPenerimaan $jenisPenerimaan): RedirectResponse
    {
        $wasAktif = $jenisPenerimaan->is_aktif;
        $jenisPenerimaan->update($request->validated());

        // Generate tagihan jika baru diaktifkan
        if (! $wasAktif && $jenisPenerimaan->is_aktif) {
            $jenisPenerimaan->load('tahunAjaran');
            $this->tagihanService->generateIuran($jenisPenerimaan);
        }

        return redirect()->route('master.jenis-penerimaan.index')
            ->with('sukses', "Jenis penerimaan '{$jenisPenerimaan->nama}' berhasil diperbarui.");
    }

    /**
     * Hapus jenis penerimaan (hanya jika belum ada pembayaran).
     */
    public function destroy(JenisPenerimaan $jenisPenerimaan): RedirectResponse
    {
        $adaPembayaran = $jenisPenerimaan->tagihanIuran()
            ->where('terbayar', '>', 0)
            ->exists();

        if ($adaPembayaran) {
            return back()->withErrors([
                'error' => "Jenis penerimaan '{$jenisPenerimaan->nama}' tidak dapat dihapus karena sudah ada pembayaran.",
            ]);
        }

        $nama = $jenisPenerimaan->nama;
        $jenisPenerimaan->delete();

        return redirect()->route('master.jenis-penerimaan.index')
            ->with('sukses', "Jenis penerimaan '{$nama}' berhasil dihapus.");
    }
}
