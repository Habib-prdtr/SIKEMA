<x-layouts.auth title="Masuk">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 p-8">

        {{-- Logo --}}
        <div class="flex flex-col items-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg">
                <svg class="w-11 h-11 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">SIKEMA</h1>
            <p class="text-gray-500 mt-1 text-sm">Sistem Informasi Keuangan Madrasah</p>
        </div>

        {{-- Error --}}
        @if($errors->any())
            <div class="alert-error mb-5">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>{{ $errors->first('login') ?? $errors->first() }}</p>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
            @csrf

            <div>
                <label for="login" class="form-label">Username / Email</label>
                <input id="login" type="text" name="login" value="{{ old('login') }}"
                    class="form-input @error('login') border-red-400 @enderror"
                    placeholder="Masukkan username atau email"
                    autocomplete="username" autofocus required>
            </div>

            <div>
                <label for="password" class="form-label">Password</label>
                <div class="relative">
                    <input id="password-input" type="password" name="password"
                        class="form-input pr-10"
                        placeholder="Masukkan password"
                        autocomplete="current-password" required>
                    <button type="button"
                        data-toggle-password="password-input"
                        class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                        <svg data-eye class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg data-eye class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center">
                <input id="remember" type="checkbox" name="remember" value="1"
                    class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500 cursor-pointer">
                <label for="remember" class="ml-2 text-sm text-gray-700 cursor-pointer">Ingat saya</label>
            </div>

            <button type="submit"
                class="w-full btn-primary justify-center py-2.5 text-base">
                Masuk
            </button>
        </form>

        {{-- Footer hint --}}
        <div class="mt-6 pt-5 border-t border-gray-100 text-center">
            <p class="text-xs text-gray-400">
                Login dengan username <span class="font-semibold text-emerald-600">admin</span>
                dan password <span class="font-semibold text-emerald-600">admin123</span>
            </p>
        </div>
    </div>
</x-layouts.auth>