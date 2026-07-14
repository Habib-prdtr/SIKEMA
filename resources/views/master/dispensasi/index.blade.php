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
                                <th>Tipe Potongan</th>
                                <th class="text-right">Nilai Potongan</th>
                                <th>Keterangan</th>
                                <th class="text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dispensasiList as $d)
                                <tr>
                                    <td class="font-medium text-gray-900">{{ $d->nama }}</td>
                                    <td>
                                        @if($d->tipe_potongan === 'persen')
                                            <span class="badge-blue">Persentase</span>
                                        @else
                                            <span class="badge-green">Nominal Rupiah</span>
                                        @endif
                                    </td>
                                    <td class="text-right font-semibold">
                                        @if($d->tipe_potongan === 'persen')
                                            {{ $d->nilai_potongan }}%
                                        @else
                                            {{ format_rupiah($d->nilai_potongan) }}
                                        @endif
                                    </td>
                                    <td class="text-gray-500 text-sm max-w-xs truncate">{{ $d->keterangan ?? '-' }}</td>
                                     <td class="text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('master.dispensasi.siswa', $d) }}" class="btn-secondary btn-sm">
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
                                                    <label class="form-label">Nama Dispensasi <span class="text-red-500">*</span></label>
                                                    <input type="text" name="nama" value="{{ $d->nama }}"
                                                        class="form-input" placeholder="Contoh: Beasiswa Prestasi" required>
                                                </div>
                                                <div>
                                                    <label class="form-label">Tipe Potongan <span class="text-red-500">*</span></label>
                                                    <select name="tipe_potongan" class="form-select tipe-potongan-select" data-id="{{ $d->id }}" required>
                                                        <option value="nominal" {{ $d->tipe_potongan === 'nominal' ? 'selected' : '' }}>Nominal Rupiah (Rp)</option>
                                                        <option value="persen" {{ $d->tipe_potongan === 'persen' ? 'selected' : '' }}>Persentase (%)</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="form-label">Nilai Potongan <span class="text-red-500">*</span></label>
                                                    <div class="relative">
                                                        <span id="prefix-edit-{{ $d->id }}" class="absolute left-3 inset-y-0 flex items-center text-gray-500 text-sm">
                                                            {{ $d->tipe_potongan === 'nominal' ? 'Rp' : '%' }}
                                                        </span>
                                                        <input type="number" name="nilai_potongan" value="{{ $d->nilai_potongan }}"
                                                            class="form-input pl-9" placeholder="0" min="0" required>
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
                <h3 class="font-semibold text-gray-900">Tambah Dispensasi</h3>
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
                        <label class="form-label">Nama Dispensasi <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}"
                            class="form-input" placeholder="Contoh: Beasiswa Yatim" required>
                    </div>
                    <div>
                        <label class="form-label">Tipe Potongan <span class="text-red-500">*</span></label>
                        <select name="tipe_potongan" id="tipe_potongan_add" class="form-select" required>
                            <option value="nominal" {{ old('tipe_potongan') === 'nominal' ? 'selected' : '' }}>Nominal Rupiah (Rp)</option>
                            <option value="persen" {{ old('tipe_potongan') === 'persen' ? 'selected' : '' }}>Persentase (%)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Nilai Potongan <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span id="prefix-add" class="absolute left-3 inset-y-0 flex items-center text-gray-500 text-sm">Rp</span>
                            <input type="number" name="nilai_potongan" value="{{ old('nilai_potongan') }}"
                                class="form-input pl-9" placeholder="0" min="0" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="form-textarea"
                            placeholder="Keterangan dispensasi">{{ old('keterangan') }}</textarea>
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
            // Add form select listener
            const tipeAdd = document.getElementById('tipe_potongan_add');
            const prefixAdd = document.getElementById('prefix-add');
            if (tipeAdd && prefixAdd) {
                tipeAdd.addEventListener('change', function() {
                    prefixAdd.textContent = this.value === 'nominal' ? 'Rp' : '%';
                });
            }

            // Edit forms select listeners
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
        });
    </script>
</x-layouts.app>
