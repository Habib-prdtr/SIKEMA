<?php

namespace App\Http\Controllers\Pengaturan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengaturan\UpdatePasswordRequest;
use App\Http\Requests\Pengaturan\UpdateSekolahRequest;
use App\Models\Sekolah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SekolahController extends Controller
{
    /**
     * Form edit profil sekolah.
     */
    public function edit(): View
    {
        $sekolah = Sekolah::getData();

        return view('pengaturan.sekolah', compact('sekolah'));
    }

    /**
     * Update profil sekolah.
     * Jika belum ada data, buat baru (first-time setup).
     */
    public function update(UpdateSekolahRequest $request): RedirectResponse
    {
        $sekolah = Sekolah::getData();

        if ($sekolah) {
            $sekolah->update($request->validated());
        } else {
            Sekolah::create($request->validated());
        }

        return redirect()->route('pengaturan.sekolah.edit')
            ->with('sukses', 'Profil sekolah berhasil diperbarui.');
    }

    /**
     * Ganti password operator yang sedang login.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('pengaturan.sekolah.edit')
            ->with('sukses', 'Password berhasil diubah.');
    }
}
