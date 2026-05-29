<x-layouts.app title="Pengaturan Sekolah">
    <x-slot:pageTitle>Pengaturan / Profil Sekolah</x-slot:pageTitle>

    <div class="max-w-2xl space-y-5">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">Profil Sekolah / Madrasah</h1>
            <p class="text-gray-500 text-sm mt-0.5">Informasi ini akan tampil di kwitansi dan laporan</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">Data Institusi</h3>
            </div>
            <form method="POST" action="{{ route('pengaturan.sekolah.update') }}" class="card-body space-y-5">
                @csrf @method('PUT')

                <div>
                    <label for="nama" class="form-label">Nama Madrasah / Sekolah <span class="text-red-500">*</span></label>
                    <input id="nama" type="text" name="nama"
                        value="{{ old('nama', $sekolah->nama ?? '') }}"
                        class="form-input @error('nama') border-red-400 @enderror"
                        placeholder="MTs Contoh Al-Hikmah" maxlength="150" required>
                    @error('nama')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="npsn" class="form-label">NPSN</label>
                    <input id="npsn" type="text" name="npsn"
                        value="{{ old('npsn', $sekolah->npsn ?? '') }}"
                        class="form-input" placeholder="8 digit NPSN" maxlength="10">
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
                    <div>
                        <label for="kepala_sekolah" class="form-label">Nama Kepala Sekolah</label>
                        <input id="kepala_sekolah" type="text" name="kepala_sekolah"
                            value="{{ old('kepala_sekolah', $sekolah->kepala_sekolah ?? '') }}"
                            class="form-input" placeholder="Nama lengkap" maxlength="100">
                    </div>
                    <div>
                        <label for="bendahara" class="form-label">Nama Bendahara</label>
                        <input id="bendahara" type="text" name="bendahara"
                            value="{{ old('bendahara', $sekolah->bendahara ?? '') }}"
                            class="form-input" placeholder="Nama lengkap" maxlength="100">
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-100 flex items-center gap-3">
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>

        {{-- Ganti Password --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">Ganti Password</h3>
            </div>
            <form method="POST" action="{{ route('pengaturan.password.update') }}" class="card-body space-y-4">
                @csrf @method('PUT')
                <div>
                    <label for="current_password" class="form-label">Password Saat Ini</label>
                    <input id="current_password" type="password" name="current_password"
                        class="form-input @error('current_password') border-red-400 @enderror">
                    @error('current_password')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="form-label">Password Baru</label>
                    <input id="password" type="password" name="password"
                        class="form-input @error('password') border-red-400 @enderror">
                    @error('password')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-input">
                </div>
                <div class="pt-2">
                    <button type="submit" class="btn-secondary">Ganti Password</button>
                </div>
            </form>
        </div>

    </div>
</x-layouts.app>
