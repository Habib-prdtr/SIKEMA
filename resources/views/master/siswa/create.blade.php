@php $isEdit = isset($siswa); @endphp

<x-layouts.app :title="$isEdit ? 'Edit Siswa' : 'Tambah Siswa'">
    <x-slot:pageTitle>Master Data / Siswa / {{ $isEdit ? 'Edit' : 'Tambah' }}</x-slot:pageTitle>

    <div class="max-w-2xl space-y-5">

        <div class="flex items-center gap-4">
            <a href="{{ route('master.siswa.index') }}" class="btn-secondary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $isEdit ? 'Edit Siswa' : 'Tambah Siswa Baru' }}</h1>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">Data Siswa</h3>
            </div>
            <form method="POST"
                action="{{ $isEdit ? route('master.siswa.update', $siswa) : route('master.siswa.store') }}"
                class="card-body space-y-5">
                @csrf
                @if($isEdit) @method('PUT') @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- No Induk --}}
                    <div>
                        <label for="no_induk" class="form-label">No. Induk <span class="text-red-500">*</span></label>
                        <input id="no_induk" type="text" name="no_induk"
                            value="{{ old('no_induk', $siswa->no_induk ?? '') }}"
                            class="form-input @error('no_induk') border-red-400 @enderror"
                            placeholder="Contoh: 2025001" maxlength="20" required>
                        <p id="no-induk-feedback" class="mt-1 text-xs font-medium hidden"></p>
                        @error('no_induk')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    {{-- Nama --}}
                    <div>
                        <label for="nama" class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input id="nama" type="text" name="nama"
                            value="{{ old('nama', $siswa->nama ?? '') }}"
                            class="form-input @error('nama') border-red-400 @enderror"
                            placeholder="Nama lengkap siswa" maxlength="100" required>
                        @error('nama')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    {{-- Kelas --}}
                    <div>
                        <label for="kelas" class="form-label">Kelas <span class="text-red-500">*</span></label>
                        <input id="kelas" type="text" name="kelas"
                            value="{{ old('kelas', $siswa->kelas ?? '') }}"
                            class="form-input @error('kelas') border-red-400 @enderror"
                            placeholder="Contoh: 7A / X IPA 1" maxlength="10" required>
                        @error('kelas')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    {{-- Asrama --}}
                    <div>
                        <label for="asrama" class="form-label">Asrama</label>
                        <input id="asrama" type="text" name="asrama"
                            value="{{ old('asrama', $siswa->asrama ?? '') }}"
                            class="form-input" placeholder="Opsional" maxlength="50">
                    </div>

                    {{-- Jenis Kelamin --}}
                    <div>
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select id="jenis_kelamin" name="jenis_kelamin"
                            class="form-select @error('jenis_kelamin') border-red-400 @enderror" required>
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin ?? '') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin ?? '') === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    {{-- Tanggal Masuk --}}
                    <div>
                        <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                        <input id="tanggal_masuk" type="date" name="tanggal_masuk"
                            value="{{ old('tanggal_masuk', isset($siswa) ? $siswa->tanggal_masuk?->format('Y-m-d') : '') }}"
                            class="form-input">
                    </div>

                    {{-- Status (Only on Edit) --}}
                    @if($isEdit)
                        <div class="sm:col-span-2">
                            <label for="status" class="form-label">Status <span class="text-red-500">*</span></label>
                            <select id="status" name="status"
                                class="form-select @error('status') border-red-400 @enderror" required>
                                <option value="aktif"    {{ old('status', $siswa->status ?? 'aktif') === 'aktif'    ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ old('status', $siswa->status ?? '')       === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                <option value="lulus"    {{ old('status', $siswa->status ?? '')       === 'lulus'    ? 'selected' : '' }}>Lulus</option>
                            </select>
                            @error('status')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                    <button type="submit" class="btn-primary">
                        {{ $isEdit ? 'Perbarui Data' : 'Simpan Siswa' }}
                    </button>
                    <a href="{{ route('master.siswa.index') }}" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const noIndukInput = document.getElementById('no_induk');
            const feedback = document.getElementById('no-induk-feedback');
            const submitBtn = document.querySelector('button[type="submit"]');
            let debounceTimeout;

            noIndukInput.addEventListener('input', function() {
                clearTimeout(debounceTimeout);
                const val = this.value.trim();

                if (!val) {
                    feedback.classList.add('hidden');
                    noIndukInput.classList.remove('border-red-400', 'border-emerald-400');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    return;
                }

                debounceTimeout = setTimeout(() => {
                    const ignoreId = "{{ $siswa->id ?? '' }}";
                    let url = `{{ route('master.siswa.cek-no-induk') }}?no_induk=${encodeURIComponent(val)}`;
                    if (ignoreId) {
                        url += `&ignore_id=${ignoreId}`;
                    }

                    fetch(url)
                        .then(res => res.json())
                        .then(data => {
                            if (data.exists) {
                                feedback.textContent = '❌ Nomor induk ini sudah digunakan!';
                                feedback.className = 'mt-1 text-xs font-medium text-red-600';
                                feedback.classList.remove('hidden');
                                noIndukInput.classList.add('border-red-400');
                                noIndukInput.classList.remove('border-emerald-400');
                                submitBtn.disabled = true;
                                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                            } else {
                                feedback.textContent = '✔ Nomor induk tersedia.';
                                feedback.className = 'mt-1 text-xs font-medium text-emerald-600';
                                feedback.classList.remove('hidden');
                                noIndukInput.classList.remove('border-red-400');
                                noIndukInput.classList.add('border-emerald-400');
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                            }
                        })
                        .catch(err => console.error(err));
                }, 300);
            });
        });
    </script>
</x-layouts.app>
