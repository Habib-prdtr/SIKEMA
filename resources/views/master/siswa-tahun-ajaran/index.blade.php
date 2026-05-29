<x-layouts.app title="Aktivasi Siswa">
    <x-slot:pageTitle>Master Data / Siswa per Tahun Ajaran</x-slot:pageTitle>

    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Aktivasi Siswa</h1>
                <p class="text-gray-500 text-sm mt-0.5">
                    Daftarkan siswa ke tahun ajaran
                    @if($tahunAktif) <span class="font-semibold text-emerald-600">{{ $tahunAktif->nama }}</span> @else
                        <span class="text-amber-600 font-medium">(belum ada tahun ajaran aktif)</span>
                    @endif
                </p>
            </div>
            @if($tahunAktif)
                <button data-modal-open="modal-aktivasi" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Aktifkan Siswa
                </button>
            @endif
        </div>

        <div class="card">
            @if($siswaList->isEmpty())
                <div class="p-12 text-center text-gray-400">
                    <svg class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <p class="text-sm">Belum ada siswa terdaftar.</p>
                    <a href="{{ route('master.siswa.create') }}" class="btn-primary mt-4">Tambah Siswa</a>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No. Induk</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Status di TA Aktif</th>
                                <th class="text-right">Tarif SPP</th>
                                <th class="text-right">Tunggakan Awal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswaList as $s)
                                @php $sta = $s->tahunAjaran->first(); @endphp
                                <tr>
                                    <td class="font-mono text-xs">{{ $s->no_induk }}</td>
                                    <td class="font-medium text-gray-900">{{ $s->nama }}</td>
                                    <td>{{ $s->kelas }}</td>
                                    <td>
                                        @if($sta)
                                            <span class="badge-green">✓ Terdaftar</span>
                                        @else
                                            <span class="badge-yellow">Belum Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-right font-medium">
                                        {{ $sta ? 'Rp ' . number_format($sta->tarif_spp, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="text-right">
                                        @if($sta && $sta->tunggakan_awal > 0)
                                            <span class="text-amber-700 font-medium">
                                                Rp {{ number_format($sta->tunggakan_awal, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($siswaList->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $siswaList->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- Modal Aktivasi --}}
    @if($tahunAktif)
    <div id="modal-aktivasi" class="modal-backdrop hidden">
        <div class="modal-box max-w-lg">
            <div class="modal-header">
                <h3 class="font-semibold text-gray-900">Aktifkan Siswa — TA {{ $tahunAktif->nama }}</h3>
                <button data-modal-close="modal-aktivasi" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('master.siswa-tahun-ajaran.store') }}">
                @csrf
                <input type="hidden" name="tahun_ajaran_id" value="{{ $tahunAktif->id }}">
                <div class="modal-body space-y-4">
                    <div>
                        <label for="siswa_id" class="form-label">Pilih Siswa <span class="text-red-500">*</span></label>
                        <select id="siswa_id" name="siswa_id" class="form-select" required>
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($siswaList as $s)
                                @if(!$s->tahunAjaran->first())
                                    <option value="{{ $s->id }}">{{ $s->no_induk }} — {{ $s->nama }} ({{ $s->kelas }})</option>
                                @endif
                            @endforeach
                        </select>
                        @error('siswa_id')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="tarif_spp" class="form-label">Tarif SPP / Bulan <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 inset-y-0 flex items-center text-gray-500 text-sm">Rp</span>
                            <input id="tarif_spp" type="number" name="tarif_spp"
                                value="{{ old('tarif_spp') }}"
                                class="form-input pl-9 @error('tarif_spp') border-red-400 @enderror"
                                placeholder="0" min="0" step="1000" required>
                        </div>
                        @error('tarif_spp')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="tunggakan_awal" class="form-label">Tunggakan Awal</label>
                        <div class="relative">
                            <span class="absolute left-3 inset-y-0 flex items-center text-gray-500 text-sm">Rp</span>
                            <input id="tunggakan_awal" type="number" name="tunggakan_awal"
                                value="{{ old('tunggakan_awal', 0) }}"
                                class="form-input pl-9" placeholder="0" min="0" step="1000">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Sisa tunggakan dari tahun ajaran sebelumnya (bisa 0)</p>
                        @error('tunggakan_awal')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="alert-info text-xs">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Setelah disimpan, sistem akan otomatis membuat 12 tagihan SPP (Jul–Jun).</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-modal-close="modal-aktivasi" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Aktifkan & Buat Tagihan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

</x-layouts.app>
