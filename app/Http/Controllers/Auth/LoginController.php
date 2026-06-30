<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Tampilkan halaman login.
     */
    public function showForm(): View|RedirectResponse
    {
        // Jika sudah login, langsung ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses login operator.
     *
     * Mendukung login via username ATAU email.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        // Pendelegasian logika bisnis (pengecekan login) ke AuthService
        if ($this->authService->attemptLogin($request->login, $request->password, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withInput($request->only('login', 'remember'))
            ->withErrors([
                'login' => 'Username/email atau password salah.',
            ]);
    }

    /**
     * Logout operator.
     */
    public function logout(Request $request): RedirectResponse
    {
        // Pendelegasian logika logout ke AuthService
        $this->authService->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
