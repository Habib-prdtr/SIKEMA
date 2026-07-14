@php
    $daftarKelas = $availableSiswa->pluck('siswa.kelas')->unique()->sort();
@endphp
<x-layouts.app title="Siswa Penerima Dispensasi">
    <x-slot:pageTitle>Master Data / Dispensasi / Siswa Penerima</x-slot:pageTitle>

    <div class="space-y-5">
        {{-- Header & Back Button --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('master.dispensasi.index') }}" class="btn-secondary btn-sm p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Siswa Penerima Dispensasi</h1>
                    <p class="text-gray-500 text-sm mt-0.5">Dispensasi: <span class="font-semibold text-blue-600">{{ $dispensasi->nama }}</span> (Potongan: {{ $dispensasi->tipe_potongan === 'persen' ? $dispensasi->nilai_potongan . '%' : format_rupiah($dispensasi->nilai_potongan) }})</p>
                </div>
            </div>
            <button data-modal-open="modal-tambah-siswa" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Siswa
            </button>
        </div>

        {{-- Table Card --}}
        <div class="card">
            @if($penerimaList->isEmpty())
                <div class="p-12 text-center text-gray-400">
                    <svg class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-sm">Belum ada siswa yang terdaftar dalam dispensasi ini pada tahun ajaran aktif.</p>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No. Induk</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Asrama</th>
                                <th class="text-center">Durasi Dispensasi</th>
                                <th class="text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penerimaList as $p)
                                <tr>
                                    <td class="font-mono text-sm text-gray-600">{{ $p->siswa->no_induk }}</td>
                                    <td class="font-medium text-gray-900">{{ $p->siswa->nama }}</td>
                                    <td>{{ $p->siswa->kelas }}</td>
                                    <td>{{ $p->siswa->asrama ?? '-' }}</td>
                                    <td class="text-center font-semibold text-blue-600">
                                        {{ $p->durasi_dispensasi }} Bulan
                                    </td>
                                    <td class="text-center">
                                        <form id="del-siswa-{{ $p->id }}" method="POST"
                                            action="{{ route('master.dispensasi.siswa.destroy', [$dispensasi, $p]) }}">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button data-confirm-delete="Cabut dispensasi {{ $dispensasi->nama }} dari siswa {{ $p->siswa->nama }}?"
                                            data-form-id="del-siswa-{{ $p->id }}"
                                            class="btn-danger btn-sm">Cabut</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Tambah Siswa --}}
    <div id="modal-tambah-siswa" class="modal-backdrop hidden">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="font-semibold text-gray-900">Beri Dispensasi ke Siswa</h3>
                <button data-modal-close="modal-tambah-siswa" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('master.dispensasi.siswa.store', $dispensasi) }}">
                @csrf
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label font-semibold text-gray-700">Pilih Kelas Terlebih Dahulu</label>
                        <select id="filter_kelas" class="form-select">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($daftarKelas as $kelas)
                                <option value="{{ $kelas }}">{{ $kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label font-semibold text-gray-700">Pilih Siswa <span class="text-red-500">*</span></label>
                        <select id="select_siswa" name="siswa_tahun_ajaran_id" class="form-select" required>
                            <option value="" data-kelas="">-- Pilih Siswa Aktif --</option>
                            @foreach($availableSiswa as $as)
                                <option value="{{ $as->id }}" data-kelas="{{ $as->siswa->kelas }}">
                                    {{ $as->siswa->no_induk }} - {{ $as->siswa->nama }} (Kelas: {{ $as->siswa->kelas }}) {{ $as->dispensasi_id ? '[Sudah ada dispensasi lain]' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-gray-400 text-xs mt-1">Siswa yang ditampilkan adalah siswa yang terdaftar aktif pada tahun ajaran {{ $tahunAktif->nama }}.</p>
                    </div>
                    <div>
                        <label class="form-label font-semibold text-gray-700">Durasi Dispensasi (Bulan) <span class="text-red-500">*</span></label>
                        <input type="number" name="durasi_dispensasi" value="{{ old('durasi_dispensasi', 12) }}"
                            class="form-input" placeholder="Contoh: 12" min="1" max="12" required>
                        <p class="text-gray-400 text-xs mt-1">Nilai potongan dispensasi akan otomatis dikurangkan pada tagihan SPP selama durasi bulan yang dipilih.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-modal-close="modal-tambah-siswa" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Berikan Dispensasi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterKelas = document.getElementById('filter_kelas');
            const selectSiswa = document.getElementById('select_siswa');

            if (filterKelas && selectSiswa) {
                // Keep a reference to the original options list
                const originalOptions = Array.from(selectSiswa.options);

                filterKelas.addEventListener('change', function() {
                    const selectedKelas = this.value;
                    
                    // Clear select options
                    selectSiswa.innerHTML = '';
                    
                    // Filter and re-append matching options
                    originalOptions.forEach(option => {
                        const optionKelas = option.getAttribute('data-kelas');
                        if (selectedKelas === '' || optionKelas === selectedKelas || option.value === '') {
                            selectSiswa.appendChild(option.cloneNode(true));
                        }
                    });
                    
                    // Reset selection
                    selectSiswa.selectedIndex = 0;
                });
            }
        });
    </script>
</x-layouts.app>
