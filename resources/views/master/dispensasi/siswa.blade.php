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
                                <th>Alamat</th>
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
                                    <td>{{ $p->siswa->alamat ?? '-' }}</td>
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
        <div class="modal-box max-w-2xl">
            <div class="modal-header">
                <div>
                    <h3 class="font-semibold text-gray-900 text-lg">Beri Dispensasi ke Siswa</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Dispensasi: <span class="font-semibold text-blue-600">{{ $dispensasi->nama }}</span> ({{ $dispensasi->tipe_potongan === 'persen' ? $dispensasi->nilai_potongan . '%' : format_rupiah($dispensasi->nilai_potongan) }})</p>
                </div>
                <button data-modal-close="modal-tambah-siswa" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('master.dispensasi.siswa.store', $dispensasi) }}">
                @csrf
                <input type="hidden" name="semester_dispensasi" id="semester_dispensasi_input" value="">

                <div class="modal-body space-y-4">
                    {{-- Step 1: Semester Selector Tabs --}}
                    <div id="step_1_container">
                        <label class="form-label font-semibold text-gray-700 mb-1.5 block">Pilih Target Semester Terlebih Dahulu <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" class="btn-tab-semester btn-secondary text-xs py-2.5 px-3 justify-center font-semibold rounded-lg border border-gray-200 hover:border-blue-400 transition-all" data-semester="ganjil">
                                📌 Semester Ganjil
                            </button>
                            <button type="button" class="btn-tab-semester btn-secondary text-xs py-2.5 px-3 justify-center font-semibold rounded-lg border border-gray-200 hover:border-blue-400 transition-all" data-semester="genap">
                                📌 Semester Genap
                            </button>
                            <button type="button" class="btn-tab-semester btn-secondary text-xs py-2.5 px-3 justify-center font-semibold rounded-lg border border-gray-200 hover:border-blue-400 transition-all" data-semester="semua">
                                📅 1 Tahun Penuh
                            </button>
                        </div>
                    </div>

                    {{-- Step 2 Container (Hidden until semester tab is clicked) --}}
                    <div id="step_2_container" class="hidden space-y-4">
                        {{-- Active Semester Banner with Change Button --}}
                        <div class="flex items-center justify-between p-2.5 rounded-lg border text-xs" id="active_semester_banner">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm" id="banner_title">📌 Semester Ganjil</span>
                                <span class="text-gray-500" id="banner_subtitle">(Juli - Desember | Maks. 6 Bulan)</span>
                            </div>
                            <button type="button" id="btn_change_semester" class="text-blue-600 hover:text-blue-800 font-semibold underline text-xs">
                                Ubah Semester
                            </button>
                        </div>

                        {{-- Durasi Dispensasi --}}
                        <div>
                            <label class="form-label font-semibold text-gray-700">Durasi Dispensasi (Bulan) <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-2 mb-2" id="preset_durasi_container">
                                <button type="button" class="btn-preset-durasi btn-secondary btn-sm" data-durasi="6">6 Bulan (Full Semester Ganjil)</button>
                                <button type="button" class="btn-preset-durasi btn-secondary btn-sm" data-durasi="3">3 Bulan</button>
                            </div>
                            <input type="number" id="durasi_dispensasi_input" name="durasi_dispensasi" value="{{ old('durasi_dispensasi') }}"
                                class="form-input" placeholder="Masukkan durasi (1-6 bulan)" min="1" max="6" required>
                        </div>

                        {{-- Filter & Search Siswa --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-gray-100">
                            <div>
                                <label class="form-label font-semibold text-gray-700">Filter Kelas</label>
                                <select id="filter_kelas" class="form-select">
                                    <option value="">-- Semua Kelas --</option>
                                    @foreach($daftarKelas as $kelas)
                                        <option value="{{ $kelas }}">{{ $kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label font-semibold text-gray-700">Cari Siswa</label>
                                <div class="relative">
                                    <input type="text" id="search_siswa_input" class="form-input pl-9" placeholder="Ketik nama atau NIS...">
                                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Header Checklist & Counter --}}
                        <div>
                            <div class="flex items-center justify-between pb-2 mb-2 border-b border-gray-200">
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input type="checkbox" id="check_all_siswa" class="form-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-xs font-semibold text-gray-700">Pilih Semua (sesuai filter)</span>
                                </label>
                                <span id="selected_count_badge" class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full border border-blue-100">0 siswa dipilih</span>
                            </div>

                            {{-- Scrollable Student List (Max 4 items visible) --}}
                            <div class="space-y-1.5 pr-1 border border-gray-200 rounded-lg p-1.5 bg-gray-50/50" style="max-height: 180px; overflow-y: auto !important;" id="student_list_container">
                                @foreach($availableSiswa as $as)
                                    @php
                                        $alreadyThis = $as->dispensasi_id === $dispensasi->id;
                                        $alreadyOther = $as->dispensasi_id && !$alreadyThis;
                                    @endphp
                                    <label class="siswa-item flex items-center justify-between py-2 px-2.5 bg-white hover:bg-blue-50/80 rounded-md border border-gray-100 cursor-pointer transition-colors"
                                        data-kelas="{{ $as->siswa->kelas }}"
                                        data-search="{{ strtolower($as->siswa->no_induk . ' ' . $as->siswa->nama . ' ' . $as->siswa->kelas) }}">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" name="siswa_tahun_ajaran_ids[]" value="{{ $as->id }}"
                                                class="siswa-checkbox form-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <div>
                                                <div class="font-medium text-gray-900 text-sm">{{ $as->siswa->nama }}</div>
                                                <div class="text-xs text-gray-500 font-mono">NIS: {{ $as->siswa->no_induk }} | Kelas: {{ $as->siswa->kelas }}</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            @if($alreadyThis)
                                                <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-blue-100 text-blue-700">
                                                    Sudah ada ({{ $as->durasi_dispensasi }} bln)
                                                </span>
                                            @elseif($alreadyOther)
                                                <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-amber-100 text-amber-800" title="Dispensasi: {{ $as->dispensasi->nama ?? '' }}">
                                                    Dispensasi lain: {{ \Illuminate\Support\Str::limit($as->dispensasi->nama ?? 'Lainnya', 15) }}
                                                </span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                                <div id="no_siswa_found" class="hidden p-6 text-center text-sm text-gray-400">
                                    Tidak ada siswa yang cocok dengan filter / pencarian.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-modal-close="modal-tambah-siswa" class="btn-secondary">Batal</button>
                    <button type="submit" id="btn_submit_dispensasi" class="btn-primary" disabled>Berikan Dispensasi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterKelas = document.getElementById('filter_kelas');
            const searchInput = document.getElementById('search_siswa_input');
            const checkAll = document.getElementById('check_all_siswa');
            const studentItems = document.querySelectorAll('.siswa-item');
            const studentCheckboxes = document.querySelectorAll('.siswa-checkbox');
            const countBadge = document.getElementById('selected_count_badge');
            const btnSubmit = document.getElementById('btn_submit_dispensasi');
            const noSiswaFound = document.getElementById('no_siswa_found');
            const durasiInput = document.getElementById('durasi_dispensasi_input');

            // Semester Tab Selector
            const semesterInput = document.getElementById('semester_dispensasi_input');
            const semesterTabs = document.querySelectorAll('.btn-tab-semester');
            const step1Container = document.getElementById('step_1_container');
            const step2Container = document.getElementById('step_2_container');
            const btnChangeSemester = document.getElementById('btn_change_semester');
            const activeSemesterBanner = document.getElementById('active_semester_banner');
            const bannerTitle = document.getElementById('banner_title');
            const bannerSubtitle = document.getElementById('banner_subtitle');
            const presetContainer = document.getElementById('preset_durasi_container');

            function bindPresetButtons() {
                document.querySelectorAll('.btn-preset-durasi').forEach(btn => {
                    btn.addEventListener('click', function() {
                        if (durasiInput) durasiInput.value = this.dataset.durasi;
                    });
                });
            }

            function updateSemesterUI(selectedSem) {
                if (!selectedSem) {
                    if (step1Container) step1Container.classList.remove('hidden');
                    if (step2Container) step2Container.classList.add('hidden');
                    if (semesterInput) semesterInput.value = '';
                    return;
                }

                if (step1Container) step1Container.classList.add('hidden');
                if (step2Container) step2Container.classList.remove('hidden');
                if (semesterInput) semesterInput.value = selectedSem;

                if (selectedSem === 'ganjil') {
                    if (durasiInput) {
                        durasiInput.max = 6;
                        durasiInput.placeholder = "Masukkan durasi (1-6 bulan)";
                        if (parseInt(durasiInput.value) > 6) durasiInput.value = 6;
                    }
                    if (presetContainer) {
                        presetContainer.innerHTML = `
                            <button type="button" class="btn-preset-durasi btn-secondary btn-sm" data-durasi="6">6 Bulan (Full Semester Ganjil)</button>
                            <button type="button" class="btn-preset-durasi btn-secondary btn-sm" data-durasi="3">3 Bulan</button>
                        `;
                    }
                    if (activeSemesterBanner) {
                        activeSemesterBanner.className = "flex items-center justify-between p-2.5 rounded-lg border border-amber-200 bg-amber-50 text-amber-900 text-xs";
                    }
                    if (bannerTitle) bannerTitle.textContent = "📌 Semester Ganjil";
                    if (bannerSubtitle) bannerSubtitle.textContent = "(Bulan Juli - Desember | Maks. 6 Bulan)";

                } else if (selectedSem === 'genap') {
                    if (durasiInput) {
                        durasiInput.max = 6;
                        durasiInput.placeholder = "Masukkan durasi (1-6 bulan)";
                        if (parseInt(durasiInput.value) > 6) durasiInput.value = 6;
                    }
                    if (presetContainer) {
                        presetContainer.innerHTML = `
                            <button type="button" class="btn-preset-durasi btn-secondary btn-sm" data-durasi="6">6 Bulan (Full Semester Genap)</button>
                            <button type="button" class="btn-preset-durasi btn-secondary btn-sm" data-durasi="3">3 Bulan</button>
                        `;
                    }
                    if (activeSemesterBanner) {
                        activeSemesterBanner.className = "flex items-center justify-between p-2.5 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-900 text-xs";
                    }
                    if (bannerTitle) bannerTitle.textContent = "📌 Semester Genap";
                    if (bannerSubtitle) bannerSubtitle.textContent = "(Bulan Januari - Juni | Maks. 6 Bulan)";

                } else {
                    if (durasiInput) {
                        durasiInput.max = 12;
                        durasiInput.placeholder = "Masukkan durasi (1-12 bulan)";
                    }
                    if (presetContainer) {
                        presetContainer.innerHTML = `
                            <button type="button" class="btn-preset-durasi btn-secondary btn-sm" data-durasi="6">1 Semester (6 Bln)</button>
                            <button type="button" class="btn-preset-durasi btn-secondary btn-sm" data-durasi="12">1 Tahun (12 Bln)</button>
                        `;
                    }
                    if (activeSemesterBanner) {
                        activeSemesterBanner.className = "flex items-center justify-between p-2.5 rounded-lg border border-blue-200 bg-blue-50 text-blue-900 text-xs";
                    }
                    if (bannerTitle) bannerTitle.textContent = "📅 1 Tahun Penuh";
                    if (bannerSubtitle) bannerSubtitle.textContent = "(Bulan Juli - Juni | Maks. 12 Bulan)";
                }

                bindPresetButtons();
            }

            semesterTabs.forEach(btn => {
                btn.addEventListener('click', function() {
                    updateSemesterUI(this.dataset.semester);
                });
            });

            if (btnChangeSemester) {
                btnChangeSemester.addEventListener('click', function() {
                    updateSemesterUI('');
                });
            }

            function filterStudents() {
                const selectedKelas = filterKelas ? filterKelas.value : '';
                const searchQueries = searchInput ? searchInput.value.toLowerCase().trim().split(' ').filter(Boolean) : [];
                let visibleCount = 0;

                studentItems.forEach(item => {
                    const itemKelas = item.getAttribute('data-kelas');
                    const itemSearch = item.getAttribute('data-search');

                    const matchKelas = !selectedKelas || itemKelas === selectedKelas;
                    const matchSearch = searchQueries.every(q => itemSearch.includes(q));

                    if (matchKelas && matchSearch) {
                        item.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        item.classList.add('hidden');
                    }
                });

                if (noSiswaFound) {
                    noSiswaFound.classList.toggle('hidden', visibleCount > 0);
                }

                updateCheckAllState();
            }

            function updateCountAndSubmit() {
                const checkedBoxes = document.querySelectorAll('.siswa-checkbox:checked');
                const totalChecked = checkedBoxes.length;

                if (countBadge) {
                    countBadge.textContent = `${totalChecked} siswa dipilih`;
                }

                if (btnSubmit) {
                    btnSubmit.disabled = totalChecked === 0;
                    btnSubmit.textContent = totalChecked > 0 
                        ? `Berikan Dispensasi (${totalChecked} Siswa)` 
                        : 'Berikan Dispensasi';
                }

                updateCheckAllState();
            }

            function updateCheckAllState() {
                if (!checkAll) return;
                const visibleCheckboxes = Array.from(studentCheckboxes).filter(cb => {
                    const parentLabel = cb.closest('.siswa-item');
                    return parentLabel && !parentLabel.classList.contains('hidden');
                });

                if (visibleCheckboxes.length === 0) {
                    checkAll.checked = false;
                    checkAll.indeterminate = false;
                    return;
                }

                const visibleChecked = visibleCheckboxes.filter(cb => cb.checked);
                if (visibleChecked.length === visibleCheckboxes.length) {
                    checkAll.checked = true;
                    checkAll.indeterminate = false;
                } else if (visibleChecked.length > 0) {
                    checkAll.checked = false;
                    checkAll.indeterminate = false;
                } else {
                    checkAll.checked = false;
                    checkAll.indeterminate = false;
                }
            }

            if (filterKelas) filterKelas.addEventListener('change', filterStudents);
            if (searchInput) searchInput.addEventListener('input', filterStudents);

            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    const isChecked = this.checked;
                    studentCheckboxes.forEach(cb => {
                        const parentLabel = cb.closest('.siswa-item');
                        if (parentLabel && !parentLabel.classList.contains('hidden')) {
                            cb.checked = isChecked;
                        }
                    });
                    updateCountAndSubmit();
                });
            }

            studentCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateCountAndSubmit);
            });

            // Initially hide step 2 until user clicks a semester button
            updateSemesterUI('');
            updateCountAndSubmit();
        });
    </script>
</x-layouts.app>
