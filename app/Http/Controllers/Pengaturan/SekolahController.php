<?php

namespace App\Http\Controllers\Pengaturan;

use App\Http\Controllers\Controller;
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
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password saat ini tidak sesuai.',
            ]);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('pengaturan.sekolah.edit')
            ->with('sukses', 'Password berhasil diubah.');
    }
}
