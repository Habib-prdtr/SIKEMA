<?php

namespace App\Http\Controllers\Pengaturan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengaturan\UpdatePasswordRequest;
use App\Http\Requests\Pengaturan\UpdateSekolahRequest;
use App\Services\PengaturanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SekolahController extends Controller
{
    public function __construct(
        private readonly PengaturanService $pengaturanService
    ) {}

    /**
     * Form edit profil sekolah.
     */
    public function edit(): View
    {
        $sekolah = $this->pengaturanService->getSekolah();

        return view('pengaturan.sekolah', compact('sekolah'));
    }

    /**
     * Update profil sekolah.
     * Jika belum ada data, buat baru (first-time setup).
     */
    public function update(UpdateSekolahRequest $request): RedirectResponse
    {
        $this->pengaturanService->simpanSekolah($request->validated());

        return redirect()->route('pengaturan.sekolah.edit')
            ->with('sukses', 'Profil sekolah berhasil diperbarui.');
    }

    /**
     * Ganti password operator yang sedang login.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $this->pengaturanService->updatePassword(
            $request->user(),
            $request->password
        );

        return redirect()->route('pengaturan.sekolah.edit')
            ->with('sukses', 'Password berhasil diubah.');
    }

    /**
     * Update fokus jenjang kelas aktif (Switch Jenjang Kelas).
     */
    public function updateJenjangKelas(Request $request): RedirectResponse
    {
        $request->validate([
            'jenjang_kelas' => ['required', 'string', 'in:semua,7,8,9'],
        ]);

        $jenjang = $request->input('jenjang_kelas');
        $this->pengaturanService->updateJenjangKelas($jenjang);

        $label = match ($jenjang) {
            '7' => 'Kelas 7',
            '8' => 'Kelas 8',
            '9' => 'Kelas 9',
            default => 'Semua Kelas',
        };

        return redirect()->back()
            ->with('sukses', "Fokus jenjang kelas berhasil diubah ke {$label}.");
    }
}
