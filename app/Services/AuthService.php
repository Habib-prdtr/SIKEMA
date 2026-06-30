<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Memproses logika pengecekan login (autentikasi) berdasarkan username atau email.
     *
     * @param string $loginField Input berupa email atau username
     * @param string $password Password pengguna
     * @param bool $remember Fitur remember me
     * @return bool True jika login berhasil, False jika gagal.
     */
    public function attemptLogin(string $loginField, string $password, bool $remember = false): bool
    {
        // Logika Bisnis: Menentukan apakah input adalah email atau username
        $credentials = filter_var($loginField, FILTER_VALIDATE_EMAIL)
            ? ['email' => $loginField, 'password' => $password]
            : ['username' => $loginField, 'password' => $password];

        // Memproses autentikasi ke database
        return Auth::attempt($credentials, $remember);
    }
    
    /**
     * Memproses logika pengeluaran (logout).
     */
    public function logout(): void
    {
        Auth::logout();
    }
}
