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
}
