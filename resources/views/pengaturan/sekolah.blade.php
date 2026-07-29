<x-layouts.app title="Pengaturan Sekolah" pageTitle="Pengaturan / Profil Sekolah">

    <div class="max-w-2xl space-y-5">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">Profil Sekolah / Madrasah</h1>
            <p class="text-gray-500 text-sm mt-0.5">Informasi ini akan tampil di kwitansi dan laporan</p>
        </div>

        <form method="POST" action="{{ route('pengaturan.sekolah.update') }}" class="space-y-5">
            @csrf @method('PUT')

            {{-- Card Data Institusi --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="font-semibold text-gray-900">Data Institusi</h3>
                </div>
                <div class="card-body space-y-5">
                    <div>
                        <label for="nama_sekolah" class="form-label">Nama Madrasah / Sekolah <span class="text-red-500">*</span></label>
                        <input id="nama_sekolah" type="text" name="nama_sekolah"
                            value="{{ old('nama_sekolah', $sekolah->nama_sekolah ?? '') }}"
                            class="form-input @error('nama_sekolah') border-red-400 @enderror"
                            placeholder="MTs Contoh Al-Hikmah" maxlength="150" required>
                        @error('nama_sekolah')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="nama_yayasan" class="form-label">Nama Yayasan</label>
                        <input id="nama_yayasan" type="text" name="nama_yayasan"
                            value="{{ old('nama_yayasan', $sekolah->nama_yayasan ?? '') }}"
                            class="form-input" placeholder="Yayasan ..." maxlength="150">
                    </div>

                    <div>
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="3" class="form-textarea"
                            placeholder="Jl. Contoh No. 1, Kota...">{{ old('alamat', $sekolah->alamat ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="telepon" class="form-label">Telepon</label>
                            <input id="telepon" type="text" name="telepon"
                                value="{{ old('telepon', $sekolah->telepon ?? '') }}"
                                class="form-input" placeholder="021-xxxxxxx">
                        </div>
                        <div>
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" name="email"
                                value="{{ old('email', $sekolah->email ?? '') }}"
                                class="form-input" placeholder="sekolah@example.com">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Data Kepala TU --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="font-semibold text-gray-900">Data Kepala Tata Usaha (TU)</h3>
                </div>
                <div class="card-body space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="kepala_tu" class="form-label">Nama Kepala TU</label>
                            <input id="kepala_tu" type="text" name="kepala_tu"
                                value="{{ old('kepala_tu', $sekolah->kepala_tu ?? '') }}"
                                class="form-input" placeholder="Nama lengkap" maxlength="100">
                        </div>
                        <div>
                            <label for="nip_kepala_tu" class="form-label">NIP Kepala TU</label>
                            <input id="nip_kepala_tu" type="text" name="nip_kepala_tu"
                                value="{{ old('nip_kepala_tu', $sekolah->nip_kepala_tu ?? '') }}"
                                class="form-input" placeholder="NIP" maxlength="30">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-2 flex items-center gap-3">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Pengaturan
                </button>
            </div>
        </form>

        {{-- Ganti Password --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">Ganti Password</h3>
            </div>
            <form method="POST" action="{{ route('pengaturan.password.update') }}" class="card-body space-y-4">
                @csrf @method('PUT')
                <div>
                    <label for="current_password" class="form-label">Password Saat Ini</label>
                    <div class="relative">
                        <input id="current_password" type="password" name="current_password"
                            class="form-input pr-10 @error('current_password') border-red-400 @enderror">
                        <button type="button" data-toggle-password="current_password" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            <svg data-eye class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg data-eye class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    @error('current_password')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="form-label">Password Baru</label>
                    <div class="relative">
                        <input id="password" type="password" name="password"
                            class="form-input pr-10 @error('password') border-red-400 @enderror">
                        <button type="button" data-toggle-password="password" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            <svg data-eye class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg data-eye class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    @error('password')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-input pr-10">
                        <button type="button" data-toggle-password="password_confirmation" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            <svg data-eye class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg data-eye class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>
                <div class="pt-2">
                    <button type="submit" class="btn-secondary">Ganti Password</button>
                </div>
            </form>
        </div>

    </div>
</x-layouts.app>
