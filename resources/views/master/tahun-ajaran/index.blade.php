<x-layouts.app title="Tahun Ajaran">
    <x-slot:pageTitle>Master Data / Tahun Ajaran</x-slot:pageTitle>

    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tahun Ajaran</h1>
                <p class="text-gray-500 text-sm mt-0.5">Kelola dan aktifkan tahun ajaran</p>
            </div>
            <button data-modal-open="modal-tambah" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Tahun Ajaran
            </button>
        </div>

        <div class="card">
            @if($tahunAjaran->isEmpty())
                <div class="p-12 text-center text-gray-400">
                    <svg class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm">Belum ada tahun ajaran. Tambahkan sekarang.</p>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Tahun Ajaran</th>
                                <th>Status</th>
                                <th>Dibuat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tahunAjaran as $i => $ta)
                                <tr>
                                    <td class="text-gray-400 text-xs">{{ $i + 1 }}</td>
                                    <td class="font-semibold text-gray-900">{{ $ta->nama }}</td>
                                    <td>
                                        @if($ta->is_aktif)
                                            <span class="badge-green">● Aktif</span>
                                        @else
                                            <span class="badge-gray">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-gray-500 text-xs">
                                        {{ $ta->created_at ? $ta->created_at->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        @if(!$ta->is_aktif)
                                            <form method="POST" action="{{ route('master.tahun-ajaran.set-aktif', $ta) }}" class="inline">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="btn-primary btn-sm"
                                                    onclick="return confirm('Aktifkan tahun ajaran {{ $ta->nama }}? Tahun ajaran lain akan dinonaktifkan.')">
                                                    Aktifkan
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Sedang aktif</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Tambah --}}
    <div id="modal-tambah" class="modal-backdrop hidden">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="font-semibold text-gray-900">Tambah Tahun Ajaran</h3>
                <button data-modal-close="modal-tambah" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('master.tahun-ajaran.store') }}">
                @csrf
                <div class="modal-body">
                    <label for="nama" class="form-label">Nama Tahun Ajaran <span class="text-red-500">*</span></label>
                    <input id="nama" type="text" name="nama" value="{{ old('nama') }}"
                        class="form-input" placeholder="Contoh: 2025/2026" required>
                    <p class="text-xs text-gray-400 mt-1.5">Format: YYYY/YYYY (contoh: 2025/2026)</p>
                    @error('nama')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="modal-footer">
                    <button type="button" data-modal-close="modal-tambah" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>
