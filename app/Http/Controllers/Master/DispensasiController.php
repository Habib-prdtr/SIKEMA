<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Dispensasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DispensasiController extends Controller
{
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
}
