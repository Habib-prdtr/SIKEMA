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
                <div class="flex gap-2">
                    <button data-modal-open="modal-aktivasi-semua" class="btn-secondary">
                        Aktifkan Semua
                    </button>
                    <button data-modal-open="modal-aktivasi" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Aktifkan Siswa
                    </button>
                </div>
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
                                <th class="text-center w-24">Aksi</th>
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
                                        @if($s->status === 'nonaktif')
                                            <span class="badge-red">Nonaktif</span>
                                        @elseif($s->status === 'lulus')
                                            <span class="badge-blue">Lulus</span>
                                        @elseif($sta)
                                            <span class="badge-green">✓ Terdaftar</span>
                                        @else
                                            <span class="badge-yellow">Belum Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-right font-medium">
                                        {{ $sta ? format_rupiah($sta->tarif_spp) : '-' }}
                                    </td>
                                    <td class="text-right">
                                        @if($sta)
                                            @php $sisa = app(App\Services\TunggakanService::class)->hitungSisa($sta); @endphp
                                            @if($sisa > 0)
                                                <span class="text-amber-700 font-medium">
                                                    {{ format_rupiah($sisa) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($sta)
                                            <button type="button" data-modal-open="modal-edit-{{ $sta->id }}"
                                                class="btn-secondary btn-sm flex items-center justify-center gap-1.5 hover:text-emerald-700 hover:border-emerald-400 mx-auto">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                <span>Edit Tarif</span>
                                            </button>
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
                                @if(!$s->tahunAjaran->first() && $s->status === 'aktif')
                                    <option value="{{ $s->id }}" data-kelas="{{ $s->kelas }}">{{ $s->no_induk }} — {{ $s->nama }} ({{ $s->kelas }})</option>
                                @endif
                            @endforeach
                        </select>
                        @error('siswa_id')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="master_tarif_spp_id" class="form-label">Tarif SPP / Bulan <span class="text-red-500">*</span></label>
                        <select id="master_tarif_spp_id" name="master_tarif_spp_id" class="form-select @error('master_tarif_spp_id') border-red-400 @enderror" required>
                            <option value="">-- Pilih Tarif SPP --</option>
                            @foreach($tarifSppList as $tarifSpp)
                                <option value="{{ $tarifSpp->id }}" data-kelas="{{ $tarifSpp->kelas }}" {{ old('master_tarif_spp_id') == $tarifSpp->id ? 'selected' : '' }}>
                                    {{ $tarifSpp->kelas }} — {{ format_rupiah($tarifSpp->tarif) }}
                                </option>
                            @endforeach
                        </select>
                        @error('master_tarif_spp_id')<p class="form-error">{{ $message }}</p>@enderror
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

    {{-- Modal Aktivasi Semua --}}
    @if($tahunAktif)
    <div id="modal-aktivasi-semua" class="modal-backdrop hidden">
        <div class="modal-box max-w-lg">
            <div class="modal-header">
                <h3 class="font-semibold text-gray-900">Aktifkan Siswa per Kelas — TA {{ $tahunAktif->nama }}</h3>
                <button data-modal-close="modal-aktivasi-semua" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('master.siswa-tahun-ajaran.storeAll') }}">
                @csrf
                <input type="hidden" name="tahun_ajaran_id" value="{{ $tahunAktif->id }}">
                <div class="modal-body space-y-4">
                    <p class="text-sm text-gray-600">Sistem akan mencoba mengaktifkan seluruh siswa yang berstatus "Aktif" pada kelas yang dipilih dan belum terdaftar di tahun ajaran ini.</p>
                    
                    <div class="bg-amber-50 border border-amber-200 p-3 rounded-lg text-xs text-amber-800">
                        <p class="font-semibold">Catatan:</p>
                        <p>Aktivasi massal ini secara otomatis menyetel tunggakan awal siswa menjadi <strong>0</strong>. Jika ada siswa yang memiliki tunggakan, silakan edit data tunggakan siswa tersebut secara terpisah setelah proses ini selesai.</p>
                    </div>

                    <div>
                        <label for="kelas_all" class="form-label">Pilih Kelas <span class="text-red-500">*</span></label>
                        <select id="kelas_all" name="kelas" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($daftarKelas as $kelas)
                                <option value="{{ $kelas }}">{{ $kelas }}</option>
                            @endforeach
                        </select>
                        @error('kelas')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="master_tarif_spp_id_all" class="form-label">Tarif SPP / Bulan <span class="text-red-500">*</span></label>
                        <select id="master_tarif_spp_id_all" name="master_tarif_spp_id" class="form-select" required>
                            <option value="">-- Pilih Tarif SPP --</option>
                            @foreach($tarifSppList as $tarifSpp)
                                <option value="{{ $tarifSpp->id }}" data-kelas="{{ $tarifSpp->kelas }}">
                                    {{ $tarifSpp->kelas }} — {{ format_rupiah($tarifSpp->tarif) }}
                                </option>
                            @endforeach
                        </select>
                        @error('master_tarif_spp_id')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-modal-close="modal-aktivasi-semua" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Aktifkan Per Kelas</button>
                </div>
            </form>
        </div>
    </div>
    @endif
    
    {{-- ... (inside script tag at the bottom) ... --}}
    <script>
        // Existing script...

        document.getElementById('kelas_all')?.addEventListener('change', function() {
            const selectedKelas = this.value;
            const tarifSppSelect = document.getElementById('master_tarif_spp_id_all');
            
            for (let i = 0; i < tarifSppSelect.options.length; i++) {
                const opt = tarifSppSelect.options[i];
                if (opt.getAttribute('data-kelas') === selectedKelas) {
                    tarifSppSelect.selectedIndex = i;
                    return;
                }
            }
            tarifSppSelect.selectedIndex = 0;
        });
    </script>


    {{-- Modal Edit SPP --}}
    @foreach($siswaList as $s)
        @php $sta = $s->tahunAjaran->first(); @endphp
        @if($sta)
        <div id="modal-edit-{{ $sta->id }}" class="modal-backdrop hidden">
            <div class="modal-box max-w-md">
                <div class="modal-header">
                    <h3 class="font-semibold text-gray-900">Ubah Tarif SPP</h3>
                    <button data-modal-close="modal-edit-{{ $sta->id }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('master.siswa-tahun-ajaran.update', $sta->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body space-y-4">
                        <div class="bg-emerald-50 border border-emerald-100 p-3 rounded-lg text-sm space-y-1">
                            <p class="text-gray-500">Nama Siswa: <span class="font-semibold text-emerald-800">{{ $s->nama }}</span></p>
                            <p class="text-gray-500">No. Induk: <span class="font-mono text-emerald-800">{{ $s->no_induk }}</span></p>
                            <p class="text-gray-500">Kelas: <span class="text-emerald-800">{{ $s->kelas }}</span></p>
                        </div>
                        <div>
                            <label for="master_tarif_spp_id_{{ $sta->id }}" class="form-label">Tarif SPP Baru / Bulan <span class="text-red-500">*</span></label>
                            <select id="master_tarif_spp_id_{{ $sta->id }}" name="master_tarif_spp_id" class="form-select @error('master_tarif_spp_id') border-red-400 @enderror" required>
                                <option value="">-- Pilih Tarif SPP --</option>
                                @foreach($tarifSppList as $tarifSpp)
                                    <option value="{{ $tarifSpp->id }}" {{ $sta->tarif_spp == $tarifSpp->tarif ? 'selected' : '' }}>
                                        {{ $tarifSpp->kelas }} — {{ format_rupiah($tarifSpp->tarif) }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Perubahan hanya akan mengubah tagihan SPP yang belum dibayar.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-modal-close="modal-edit-{{ $sta->id }}" class="btn-secondary">Batal</button>
                        <button type="submit" class="btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    @endforeach

    <script>
        document.getElementById('siswa_id')?.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const studentKelas = selectedOption.getAttribute('data-kelas'); // e.g. "7A"
            if (studentKelas) {
                const tarifSppSelect = document.getElementById('master_tarif_spp_id');
                if (!tarifSppSelect) return;

                // Try digit matching first (e.g. "7A" matching "7" or "Kelas 7")
                const match = studentKelas.match(/\d+/);
                if (match) {
                    const gradeNum = match[0];
                    for (let i = 0; i < tarifSppSelect.options.length; i++) {
                        const opt = tarifSppSelect.options[i];
                        const optText = (opt.getAttribute('data-kelas') || opt.text).toLowerCase();
                        const optMatch = optText.match(/\d+/);
                        if (optMatch && optMatch[0] === gradeNum) {
                            tarifSppSelect.selectedIndex = i;
                            return;
                        }
                    }
                }

                // Fallback: search for Roman numerals (VII, VIII, IX)
                const romanMap = { 'vii': '7', 'viii': '8', 'ix': '9', 'x': '10', 'xi': '11', 'xii': '12' };
                let normalizedStudent = studentKelas.toLowerCase();
                for (const [roman, num] of Object.entries(romanMap)) {
                    if (normalizedStudent.includes(roman)) {
                        normalizedStudent = normalizedStudent.replace(roman, num);
                        break;
                    }
                }

                const studentMatch = normalizedStudent.match(/\d+/);
                const searchNum = studentMatch ? studentMatch[0] : null;

                for (let i = 0; i < tarifSppSelect.options.length; i++) {
                    const opt = tarifSppSelect.options[i];
                    let optText = (opt.getAttribute('data-kelas') || opt.text).toLowerCase();
                    for (const [roman, num] of Object.entries(romanMap)) {
                        if (optText.includes(roman)) {
                            optText = optText.replace(roman, num);
                            break;
                        }
                    }
                    const optMatch = optText.match(/\d+/);
                    if (searchNum && optMatch && optMatch[0] === searchNum) {
                        tarifSppSelect.selectedIndex = i;
                        return;
                    }
                }
            }
        });
    </script>

</x-layouts.app>
