<x-layouts.app title="Master Data Dispensasi">
    <x-slot:pageTitle>Master Data / Dispensasi</x-slot:pageTitle>

    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Master Data Dispensasi</h1>
                <p class="text-gray-500 text-sm mt-0.5">Kelola tipe dan potongan dispensasi SPP siswa</p>
            </div>
            <button data-modal-open="modal-tambah" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Dispensasi
            </button>
        </div>

        <div class="card">
            @if($dispensasiList->isEmpty())
                <div class="p-12 text-center text-gray-400">
                    <svg class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm">Belum ada master data dispensasi.</p>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Dispensasi</th>
                                <th>Target Penerimaan</th>
                                <th>Tipe Potongan</th>
                                <th class="text-right">Nilai Potongan</th>
                                <th>Keterangan</th>
                                <th class="text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dispensasiList as $d)
                                <tr>
                                    <td class="font-semibold text-gray-900">{{ $d->nama }}</td>
                                    <td>
                                        @if(empty($d->jenis_penerimaan_id))
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                                <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                SPP Bulanan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                                Iuran: {{ $d->target_nama }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($d->tipe_potongan === 'persen')
                                            <span class="badge-blue font-medium">Persentase (%)</span>
                                        @else
                                            <span class="badge-green font-medium">Nominal Rupiah</span>
                                        @endif
                                    </td>
                                    <td class="text-right font-bold text-gray-900">
                                        @if($d->tipe_potongan === 'persen')
                                            <span class="text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">{{ $d->nilai_potongan }}%</span>
                                        @else
                                            <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">{{ format_rupiah($d->nilai_potongan) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-gray-500 text-sm max-w-xs truncate">{{ $d->keterangan ?? '-' }}</td>
                                     <td class="text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('master.dispensasi.siswa', $d) }}" class="btn-secondary btn-sm flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                </svg>
                                                Siswa ({{ $d->siswaTahunAjaran()->count() }})
                                            </a>
                                            <button class="btn-secondary btn-sm"
                                                data-modal-open="modal-edit-{{ $d->id }}">Edit</button>
                                            <form id="del-d-{{ $d->id }}" method="POST"
                                                action="{{ route('master.dispensasi.destroy', $d) }}">
                                                @csrf @method('DELETE')
                                            </form>
                                            <button data-confirm-delete="Hapus dispensasi {{ $d->nama }}?"
                                                data-form-id="del-d-{{ $d->id }}"
                                                class="btn-danger btn-sm">Hapus</button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Modal Edit --}}
                                <div id="modal-edit-{{ $d->id }}" class="modal-backdrop hidden">
                                    <div class="modal-box">
                                        <div class="modal-header">
                                            <h3 class="font-semibold text-gray-900">Edit Dispensasi</h3>
                                            <button data-modal-close="modal-edit-{{ $d->id }}"
                                                class="text-gray-400 hover:text-gray-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <form method="POST" action="{{ route('master.dispensasi.update', $d) }}">
                                            @csrf @method('PUT')
                                            <div class="modal-body space-y-4">
                                                <div>
                                                    <label class="form-label font-semibold">Nama Dispensasi <span class="text-red-500">*</span></label>
                                                    <input type="text" name="nama" value="{{ $d->nama }}"
                                                        class="form-input" placeholder="Contoh: Beasiswa Prestasi" required>
                                                </div>
                                                <div>
                                                    <label class="form-label font-semibold">Target Jenis Penerimaan <span class="text-red-500">*</span></label>
                                                    <select name="jenis_penerimaan_id" class="form-select target-penerimaan-select" data-info-id="info-edit-target-{{ $d->id }}">
                                                        <optgroup label="Penerimaan Utama">
                                                            <option value="" {{ empty($d->jenis_penerimaan_id) ? 'selected' : '' }}>💳 SPP (SPP Bulanan)</option>
                                                        </optgroup>
                                                        @if($jenisPenerimaanOptions->isNotEmpty())
                                                            <optgroup label="Iuran & Penerimaan Lainnya">
                                                                @foreach($jenisPenerimaanOptions as $jp)
                                                                    <option value="{{ $jp->id }}" {{ $d->jenis_penerimaan_id == $jp->id ? 'selected' : '' }}>
                                                                        🏷️ {{ $jp->nama }} {{ $jp->tahunAjaran ? '('.$jp->tahunAjaran->nama.')' : '' }} — {{ format_rupiah($jp->tarif) }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endif
                                                    </select>
                                                    <div id="info-edit-target-{{ $d->id }}" class="mt-2 p-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-start gap-2">
                                                        <svg class="w-4 h-4 text-purple-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                        <span class="info-text">Dispensasi ini dipasang untuk memotong tagihan **SPP Bulanan** siswa.</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="form-label font-semibold">Tipe Potongan <span class="text-red-500">*</span></label>
                                                    <select name="tipe_potongan" class="form-select tipe-potongan-select" data-id="{{ $d->id }}" required>
                                                        <option value="nominal" {{ $d->tipe_potongan === 'nominal' ? 'selected' : '' }}>Nominal Rupiah (Rp)</option>
                                                        <option value="persen" {{ $d->tipe_potongan === 'persen' ? 'selected' : '' }}>Persentase (%)</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="form-label font-semibold">Nilai Potongan <span class="text-red-500">*</span></label>
                                                    <div class="relative">
                                                        <span id="prefix-edit-{{ $d->id }}" class="absolute left-3 inset-y-0 flex items-center text-gray-500 text-sm font-semibold">
                                                            {{ $d->tipe_potongan === 'nominal' ? 'Rp' : '%' }}
                                                        </span>
                                                        <input type="number" name="nilai_potongan" value="{{ $d->nilai_potongan }}"
                                                            class="form-input pl-9 font-medium" placeholder="0" min="0" required>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="form-label">Keterangan</label>
                                                    <textarea name="keterangan" rows="2" class="form-textarea"
                                                        placeholder="Keterangan dispensasi">{{ $d->keterangan }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" data-modal-close="modal-edit-{{ $d->id }}" class="btn-secondary">Batal</button>
                                                <button type="submit" class="btn-primary">Perbarui</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
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
                <h3 class="font-semibold text-gray-900">Tambah Dispensasi Baru</h3>
                <button data-modal-close="modal-tambah" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('master.dispensasi.store') }}">
                @csrf
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label font-semibold">Nama Dispensasi <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}"
                            class="form-input" placeholder="Contoh: Beasiswa Yatim" required>
                    </div>
                    <div>
                        <label class="form-label font-semibold">Target Jenis Penerimaan <span class="text-red-500">*</span></label>
                        <select name="jenis_penerimaan_id" id="jenis_penerimaan_add" class="form-select target-penerimaan-select" data-info-id="info-add-target">
                            <optgroup label="Penerimaan Utama">
                                <option value="" {{ old('jenis_penerimaan_id') == '' ? 'selected' : '' }}>💳 SPP (SPP Bulanan)</option>
                            </optgroup>
                            @if($jenisPenerimaanOptions->isNotEmpty())
                                <optgroup label="Iuran & Penerimaan Lainnya">
                                    @foreach($jenisPenerimaanOptions as $jp)
                                        <option value="{{ $jp->id }}" {{ old('jenis_penerimaan_id') == $jp->id ? 'selected' : '' }}>
                                            🏷️ {{ $jp->nama }} {{ $jp->tahunAjaran ? '('.$jp->tahunAjaran->nama.')' : '' }} — {{ format_rupiah($jp->tarif) }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                        <div id="info-add-target" class="mt-2 p-2.5 rounded-lg bg-purple-50 border border-purple-200 text-xs text-purple-800 flex items-start gap-2">
                            <svg class="w-4 h-4 text-purple-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="info-text">Dispensasi ini dipasang untuk memotong tagihan <strong>SPP Bulanan</strong> siswa.</span>
                        </div>
                    </div>
                    <div>
                        <label class="form-label font-semibold">Tipe Potongan <span class="text-red-500">*</span></label>
                        <select name="tipe_potongan" id="tipe_potongan_add" class="form-select" required>
                            <option value="nominal" {{ old('tipe_potongan') === 'nominal' ? 'selected' : '' }}>Nominal Rupiah (Rp)</option>
                            <option value="persen" {{ old('tipe_potongan') === 'persen' ? 'selected' : '' }}>Persentase (%)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label font-semibold">Nilai Potongan <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span id="prefix-add" class="absolute left-3 inset-y-0 flex items-center text-gray-500 text-sm font-semibold">Rp</span>
                            <input type="number" name="nilai_potongan" value="{{ old('nilai_potongan') }}"
                                class="form-input pl-9 font-medium" placeholder="0" min="0" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="form-textarea"
                            placeholder="Keterangan dispensasi (opsional)">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-modal-close="modal-tambah" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add form select listener for prefix
            const tipeAdd = document.getElementById('tipe_potongan_add');
            const prefixAdd = document.getElementById('prefix-add');
            if (tipeAdd && prefixAdd) {
                tipeAdd.addEventListener('change', function() {
                    prefixAdd.textContent = this.value === 'nominal' ? 'Rp' : '%';
                });
            }

            // Edit forms select listeners for prefix
            const editSelects = document.querySelectorAll('.tipe-potongan-select');
            editSelects.forEach(select => {
                select.addEventListener('change', function() {
                    const id = this.getAttribute('data-id');
                    const prefixEdit = document.getElementById('prefix-edit-' + id);
                    if (prefixEdit) {
                        prefixEdit.textContent = this.value === 'nominal' ? 'Rp' : '%';
                    }
                });
            });

            // Target Penerimaan Helper Info Updater
            const targetSelects = document.querySelectorAll('.target-penerimaan-select');
            targetSelects.forEach(select => {
                function updateTargetInfo() {
                    const infoId = select.getAttribute('data-info-id');
                    const infoBox = document.getElementById(infoId);
                    if (!infoBox) return;

                    const infoText = infoBox.querySelector('.info-text');
                    const val = select.value;
                    const selectedOption = select.options[select.selectedIndex];
                    const label = selectedOption ? selectedOption.text.replace(/^[^\w\s]+/, '').trim() : '';

                    if (!val) {
                        infoBox.className = "mt-2 p-2.5 rounded-lg bg-purple-50 border border-purple-200 text-xs text-purple-800 flex items-start gap-2";
                        if (infoText) infoText.innerHTML = "Dispensasi ini dipasang untuk memotong tagihan <strong>SPP Bulanan</strong> siswa.";
                    } else {
                        infoBox.className = "mt-2 p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-start gap-2";
                        if (infoText) infoText.innerHTML = `Dispensasi ini dipasang untuk memotong tagihan iuran <strong>${label}</strong> siswa.`;
                    }
                }

                select.addEventListener('change', updateTargetInfo);
                updateTargetInfo(); // Initial call
            });
        });
    </script>
</x-layouts.app>
