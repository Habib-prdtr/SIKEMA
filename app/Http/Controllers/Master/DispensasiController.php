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

        $dispensasi->update($validated);

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

        // Ambil semua siswa yang aktif di tahun ajaran aktif dan memiliki dispensasi ini
        $penerimaList = SiswaTahunAjaran::where('tahun_ajaran_id', $tahunAktif->id)
            ->where('dispensasi_id', $dispensasi->id)
            ->with('siswa')
            ->get();

        // Ambil siswa yang aktif di tahun ajaran ini tetapi belum memiliki dispensasi ini
        $availableSiswa = SiswaTahunAjaran::where('tahun_ajaran_id', $tahunAktif->id)
            ->where(function ($query) use ($dispensasi) {
                $query->whereNull('dispensasi_id')
                    ->orWhere('dispensasi_id', '!=', $dispensasi->id);
            })
            ->with('siswa')
            ->get()
            ->sortBy('siswa.nama');

        return view('master.dispensasi.siswa', compact('dispensasi', 'tahunAktif', 'penerimaList', 'availableSiswa'));
    }

    /**
     * Berikan dispensasi ini kepada siswa untuk tahun ajaran aktif.
     */
    public function tambahSiswa(Request $request, Dispensasi $dispensasi): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_tahun_ajaran_id' => 'required|exists:siswa_tahun_ajaran,id',
            'durasi_dispensasi' => 'required|integer|min:1|max:12',
        ], [
            'siswa_tahun_ajaran_id.required' => 'Siswa wajib dipilih.',
            'durasi_dispensasi.required' => 'Durasi dispensasi wajib diisi.',
            'durasi_dispensasi.min' => 'Durasi minimal 1 bulan.',
            'durasi_dispensasi.max' => 'Durasi maksimal 12 bulan.',
        ]);

        $sta = SiswaTahunAjaran::findOrFail($validated['siswa_tahun_ajaran_id']);

        try {
            $this->masterDataService->assignDispensasiKeSiswa($sta, $dispensasi->id, $validated['durasi_dispensasi']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memberikan dispensasi: ' . $e->getMessage()]);
        }

        return redirect()->route('master.dispensasi.siswa', $dispensasi)
            ->with('sukses', "Dispensasi berhasil diberikan kepada siswa {$sta->siswa->nama}.");
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
