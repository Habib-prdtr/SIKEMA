<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Dispensasi;
use App\Models\SiswaTahunAjaran;
use App\Models\TahunAjaran;
use App\Services\MasterDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DispensasiController extends Controller
{
    public function __construct(
        private readonly MasterDataService $masterDataService
    ) {}
    /**
     * Tampilkan daftar master data dispensasi.
     */
    public function index(): View
    {
        $dispensasiList = Dispensasi::orderBy('nama')->get();

        return view('master.dispensasi.index', compact('dispensasiList'));
    }

    /**
     * Simpan master data dispensasi baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'nama' => 'required|string|max:100',
            'tipe_potongan' => 'required|in:persen,nominal',
            'nilai_potongan' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ];

        if ($request->input('tipe_potongan') === 'persen') {
            $rules['nilai_potongan'] .= '|max:100';
        }

        $validated = $request->validate($rules, [
            'nama.required' => 'Nama dispensasi wajib diisi.',
            'nilai_potongan.required' => 'Nilai potongan wajib diisi.',
            'nilai_potongan.max' => 'Potongan persentase tidak boleh melebihi 100%.',
        ]);

        Dispensasi::create($validated);

        return redirect()->route('master.dispensasi.index')
            ->with('sukses', 'Master data dispensasi berhasil ditambahkan.');
    }

    /**
     * Perbarui master data dispensasi.
     */
    public function update(Request $request, Dispensasi $dispensasi): RedirectResponse
    {
        $rules = [
            'nama' => 'required|string|max:100',
            'tipe_potongan' => 'required|in:persen,nominal',
            'nilai_potongan' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ];

        if ($request->input('tipe_potongan') === 'persen') {
            $rules['nilai_potongan'] .= '|max:100';
        }

        $validated = $request->validate($rules, [
            'nama.required' => 'Nama dispensasi wajib diisi.',
            'nilai_potongan.required' => 'Nilai potongan wajib diisi.',
            'nilai_potongan.max' => 'Potongan persentase tidak boleh melebihi 100%.',
        ]);

        $this->masterDataService->updateDispensasi($dispensasi, $validated);

        return redirect()->route('master.dispensasi.index')
            ->with('sukses', 'Master data dispensasi berhasil diperbarui.');
    }

    /**
     * Hapus master data dispensasi.
     */
    public function destroy(Dispensasi $dispensasi): RedirectResponse
    {
        // Cek apakah ada siswa yang sedang menggunakan dispensasi ini
        $usedCount = $dispensasi->siswaTahunAjaran()->count();
        if ($usedCount > 0) {
            return back()->withErrors([
                'error' => "Dispensasi '{$dispensasi->nama}' tidak dapat dihapus karena sedang digunakan oleh {$usedCount} data siswa.",
            ]);
        }

        $dispensasi->delete();

        return redirect()->route('master.dispensasi.index')
            ->with('sukses', 'Master data dispensasi berhasil dihapus.');
    }

    /**
     * Tampilkan siswa penerima dispensasi ini di tahun ajaran aktif.
     */
    public function siswa(Dispensasi $dispensasi): View
    {
        $tahunAktif = TahunAjaran::aktif();
        if (!$tahunAktif) {
            abort(404, 'Tahun ajaran aktif belum ditentukan.');
        }

        $ganjilBulan = [7, 8, 9, 10, 11, 12];
        $genapBulan = [1, 2, 3, 4, 5, 6];

        // Ambil semua siswa yang aktif di tahun ajaran aktif dan memiliki dispensasi ini
        $penerimaList = SiswaTahunAjaran::where('tahun_ajaran_id', $tahunAktif->id)
            ->where('dispensasi_id', $dispensasi->id)
            ->with(['siswa', 'tagihanSpp'])
            ->get();

        foreach ($penerimaList as $p) {
            $tarif = $p->tarif_spp;
            $p->durasi_ganjil = $p->tagihanSpp
                ->filter(fn($b) => in_array((int)$b->bulan, $ganjilBulan, true) && $b->tagihan < $tarif)
                ->count();
            $p->durasi_genap = $p->tagihanSpp
                ->filter(fn($b) => in_array((int)$b->bulan, $genapBulan, true) && $b->tagihan < $tarif)
                ->count();
            $p->total_durasi = $p->durasi_ganjil + $p->durasi_genap;
        }

        // Ambil semua siswa yang aktif di tahun ajaran ini
        $availableSiswa = SiswaTahunAjaran::where('tahun_ajaran_id', $tahunAktif->id)
            ->with(['siswa', 'dispensasi', 'tagihanSpp'])
            ->get()
            ->sortBy('siswa.nama');

        foreach ($availableSiswa as $as) {
            $tarif = $as->tarif_spp;
            $as->durasi_ganjil = $as->tagihanSpp
                ->filter(fn($b) => in_array((int)$b->bulan, $ganjilBulan, true) && $b->tagihan < $tarif)
                ->count();
            $as->durasi_genap = $as->tagihanSpp
                ->filter(fn($b) => in_array((int)$b->bulan, $genapBulan, true) && $b->tagihan < $tarif)
                ->count();
            $as->total_durasi = $as->durasi_ganjil + $as->durasi_genap;
        }

        return view('master.dispensasi.siswa', compact('dispensasi', 'tahunAktif', 'penerimaList', 'availableSiswa'));
    }

    /**
     * Berikan dispensasi ini kepada siswa untuk tahun ajaran aktif.
     */
    public function tambahSiswa(Request $request, Dispensasi $dispensasi): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_tahun_ajaran_id' => 'nullable|exists:siswa_tahun_ajaran,id',
            'siswa_tahun_ajaran_ids' => 'nullable|array',
            'siswa_tahun_ajaran_ids.*' => 'exists:siswa_tahun_ajaran,id',
            'durasi_dispensasi' => 'required|integer|min:1|max:12',
            'semester_dispensasi' => 'nullable|in:ganjil,genap,semua',
        ], [
            'durasi_dispensasi.required' => 'Durasi dispensasi wajib diisi.',
            'durasi_dispensasi.min' => 'Durasi minimal 1 bulan.',
            'durasi_dispensasi.max' => 'Durasi maksimal 12 bulan.',
        ]);

        $semester = $validated['semester_dispensasi'] ?? 'semua';
        if (in_array($semester, ['ganjil', 'genap']) && (int) $validated['durasi_dispensasi'] > 6) {
            return back()->withErrors(['durasi_dispensasi' => 'Durasi dispensasi per semester maksimal 6 bulan.']);
        }

        $ids = [];
        if (!empty($validated['siswa_tahun_ajaran_ids'])) {
            $ids = $validated['siswa_tahun_ajaran_ids'];
        } elseif (!empty($validated['siswa_tahun_ajaran_id'])) {
            $ids = [$validated['siswa_tahun_ajaran_id']];
        }

        if (empty($ids)) {
            return back()->withErrors(['error' => 'Pilih minimal satu siswa.']);
        }

        try {
            $count = 0;
            foreach ($ids as $staId) {
                $sta = SiswaTahunAjaran::findOrFail($staId);
                $this->masterDataService->assignDispensasiKeSiswa($sta, $dispensasi->id, (int) $validated['durasi_dispensasi'], $semester);
                $count++;
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memberikan dispensasi: ' . $e->getMessage()]);
        }

        $pesan = $count === 1
            ? "Dispensasi berhasil diberikan kepada siswa."
            : "Dispensasi berhasil diberikan kepada {$count} siswa.";

        return redirect()->route('master.dispensasi.siswa', $dispensasi)
            ->with('sukses', $pesan);
    }

    /**
     * Cabut/hapus dispensasi dari siswa.
     */
    public function hapusSiswa(Dispensasi $dispensasi, SiswaTahunAjaran $siswaTahunAjaran): RedirectResponse
    {
        try {
            $this->masterDataService->assignDispensasiKeSiswa($siswaTahunAjaran, null, 0);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mencabut dispensasi: ' . $e->getMessage()]);
        }

        return redirect()->route('master.dispensasi.siswa', $dispensasi)
            ->with('sukses', "Dispensasi untuk siswa {$siswaTahunAjaran->siswa->nama} berhasil dicabut.");
    }
}
