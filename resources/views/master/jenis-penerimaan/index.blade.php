<x-layouts.app title="Jenis Penerimaan">
    <x-slot:pageTitle>Master Data / Jenis Penerimaan</x-slot:pageTitle>

    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Jenis Penerimaan (Iuran)</h1>
                <p class="text-gray-500 text-sm mt-0.5">Kelola jenis iuran selain SPP</p>
            </div>
            @if($tahunFilter)
                <button data-modal-open="modal-tambah" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Jenis
                </button>
            @endif
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('master.jenis-penerimaan.index') }}" class="card p-4">
            <div class="flex items-end gap-3">
                <div class="w-64">
                    <label class="form-label text-xs font-semibold text-gray-500 mb-1">Pilih Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" class="form-select" onchange="this.form.submit()">
                        @foreach($tahunList as $ta)
                            <option value="{{ $ta->id }}" {{ $selectedTahunId == $ta->id ? 'selected' : '' }}>
                                {{ $ta->nama }} {{ $ta->is_aktif ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if($selectedTahunId != $tahunAktif?->id)
                    <a href="{{ route('master.jenis-penerimaan.index') }}" class="btn-secondary">
                        Reset ke Tahun Aktif
                    </a>
                @endif
            </div>
        </form>

        @if(!$tahunFilter)
            <div class="alert-warning">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>Aktifkan atau pilih tahun ajaran terlebih dahulu untuk mengelola jenis penerimaan.</p>
            </div>
        @endif

        <div class="card">
            @if($jenisPenerimaan->isEmpty())
                <div class="p-12 text-center text-gray-400">
                    <svg class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-sm">Belum ada jenis penerimaan.</p>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Urutan</th>
                                <th>Nama Iuran</th>
                                <th class="text-right">Tarif</th>
                                <th class="text-right">Total Terkumpul</th>
                                <th class="text-center">Status</th>
                                <th>Keterangan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jenisPenerimaan as $jp)
                                <tr>
                                    <td class="text-center font-bold text-gray-500 w-16">{{ $jp->urutan }}</td>
                                    <td class="font-medium text-gray-900">{{ $jp->nama }}</td>
                                    <td class="text-right font-semibold text-emerald-700">
                                        {{ format_rupiah($jp->tarif) }}
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('master.jenis-penerimaan.pembayar', $jp) }}" 
                                           class="inline-flex items-center gap-1 font-bold text-emerald-700 hover:text-emerald-800 hover:underline cursor-pointer"
                                           title="Lihat rincian pembayar">
                                            <span>{{ format_rupiah($jp->total_terkumpul ?? 0) }}</span>
                                            <svg class="w-3.5 h-3.5 opacity-60 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        @if($jp->is_aktif)
                                            <span class="badge-green">Aktif</span>
                                        @else
                                            <span class="badge-gray">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-gray-500 text-sm max-w-xs truncate">{{ $jp->keterangan ?? '-' }}</td>
                                    <td class="text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button class="btn-secondary btn-sm"
                                                data-modal-open="modal-edit-{{ $jp->id }}"
                                                data-edit-fill="{{ json_encode(['nama' => $jp->nama, 'tarif' => $jp->tarif, 'urutan' => $jp->urutan, 'keterangan' => $jp->keterangan]) }}"
                                                data-edit-form="form-edit-{{ $jp->id }}">
                                                Edit
                                            </button>
                                            <form id="del-jp-{{ $jp->id }}" method="POST"
                                                action="{{ route('master.jenis-penerimaan.destroy', $jp) }}">
                                                @csrf @method('DELETE')
                                            </form>
                                            <button data-confirm-delete="Hapus jenis penerimaan {{ $jp->nama }}?"
                                                data-form-id="del-jp-{{ $jp->id }}"
                                                class="btn-danger btn-sm">Hapus</button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Modal Edit per item --}}
                                <div id="modal-edit-{{ $jp->id }}" class="modal-backdrop hidden">
                                    <div class="modal-box">
                                        <div class="modal-header">
                                            <h3 class="font-semibold text-gray-900">Edit Jenis Penerimaan</h3>
                                            <button data-modal-close="modal-edit-{{ $jp->id }}"
                                                class="text-gray-400 hover:text-gray-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <form id="form-edit-{{ $jp->id }}" method="POST"
                                            action="{{ route('master.jenis-penerimaan.update', $jp) }}">
                                            @csrf @method('PUT')
                                            <div class="modal-body space-y-4">
                                                <div>
                                                    <label class="form-label">Nama Iuran <span class="text-red-500">*</span></label>
                                                    <input type="text" name="nama" value="{{ $jp->nama }}"
                                                        class="form-input" maxlength="100" required>
                                                </div>
                                                <div>
                                                    <label class="form-label">Tarif <span class="text-red-500">*</span></label>
                                                    <div class="relative">
                                                        <span class="absolute left-3 inset-y-0 flex items-center text-gray-500 text-sm">Rp</span>
                                                        <input type="number" name="tarif" value="{{ $jp->tarif }}"
                                                            class="form-input pl-9" min="0" step="1000" required>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="form-label">Urutan (1-15) <span class="text-red-500">*</span></label>
                                                    <input type="number" name="urutan" value="{{ $jp->urutan }}"
                                                        class="form-input" min="1" max="15" required>
                                                </div>
                                                <div>
                                                    <label class="form-label">Keterangan</label>
                                                    <textarea name="keterangan" rows="2"
                                                        class="form-textarea">{{ $jp->keterangan }}</textarea>
                                                </div>
                                                <div class="flex items-center gap-2 pt-2">
                                                    <input type="checkbox" name="is_aktif" id="edit-is-aktif-{{ $jp->id }}" value="1"
                                                        {{ $jp->is_aktif ? 'checked' : '' }}
                                                        class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                                    <label for="edit-is-aktif-{{ $jp->id }}" class="text-sm font-medium text-gray-700">Aktifkan Iuran (Jika aktif, tagihan iuran akan muncul untuk siswa)</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" data-modal-close="modal-edit-{{ $jp->id }}" class="btn-secondary">Batal</button>
                                                <button type="submit" class="btn-primary">Simpan</button>
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
    @if($tahunFilter)
    <div id="modal-tambah" class="modal-backdrop hidden">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="font-semibold text-gray-900">Tambah Jenis Penerimaan</h3>
                <button data-modal-close="modal-tambah" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('master.jenis-penerimaan.store') }}">
                @csrf
                <input type="hidden" name="tahun_ajaran_id" value="{{ $tahunFilter->id }}">
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">Nama Iuran <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}"
                            class="form-input" placeholder="Contoh: Buku Paket" maxlength="100" required>
                        @error('nama')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Tarif <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 inset-y-0 flex items-center text-gray-500 text-sm">Rp</span>
                            <input type="number" name="tarif" value="{{ old('tarif') }}"
                                class="form-input pl-9" placeholder="0" min="0" step="1000" required>
                        </div>
                        @error('tarif')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Urutan (1-15) <span class="text-red-500">*</span></label>
                        <input type="number" name="urutan" value="{{ old('urutan', $jenisPenerimaan->count() + 1) }}"
                            class="form-input" min="1" max="15" required>
                        @error('urutan')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="form-textarea"
                            placeholder="Keterangan (opsional)">{{ old('keterangan') }}</textarea>
                    </div>
                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="is_aktif" id="add-is-aktif" value="1" checked
                            class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                        <label for="add-is-aktif" class="text-sm font-medium text-gray-700">Aktifkan Iuran (Jika aktif, tagihan iuran akan muncul untuk siswa)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-modal-close="modal-tambah" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

</x-layouts.app>
