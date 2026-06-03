<x-layouts.app title="Pos Biaya">
    <x-slot:pageTitle>Master Data / Pos Biaya</x-slot:pageTitle>

    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pos Biaya</h1>
                <p class="text-gray-500 text-sm mt-0.5">Kelola pos/kategori pengeluaran</p>
            </div>
            @if($tahunAktif)
                <button data-modal-open="modal-tambah" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Pos
                </button>
            @endif
        </div>

        @if(!$tahunAktif)
            <div class="alert-warning">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>Aktifkan tahun ajaran terlebih dahulu untuk mengelola pos biaya.</p>
            </div>
        @endif

        <div class="card">
            @if($posBiaya->isEmpty())
                <div class="p-12 text-center text-gray-400">
                    <svg class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    <p class="text-sm">Belum ada pos biaya.</p>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Pos</th>
                                <th class="text-right">Anggaran</th>
                                <th class="text-right">Total Terpakai</th>
                                <th>Keterangan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($posBiaya as $pb)
                                @php $terpakai = (int) ($pb->pengeluaran_sum_jumlah ?? 0); @endphp
                                <tr>
                                    <td class="font-medium text-gray-900">{{ $pb->nama }}</td>
                                    <td class="text-right font-medium">
                                        {{ format_rupiah($pb->anggaran) }}
                                    </td>
                                    <td class="text-right">
                                        <span class="{{ $terpakai > $pb->anggaran ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                            {{ format_rupiah($terpakai) }}
                                        </span>
                                        @if($pb->anggaran > 0)
                                            <span class="text-xs text-gray-400 ml-1">
                                                ({{ number_format($terpakai / $pb->anggaran * 100, 0) }}%)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-gray-500 text-sm max-w-xs truncate">{{ $pb->keterangan ?? '-' }}</td>
                                    <td class="text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button class="btn-secondary btn-sm"
                                                data-modal-open="modal-edit-{{ $pb->id }}">Edit</button>
                                            <form id="del-pb-{{ $pb->id }}" method="POST"
                                                action="{{ route('master.pos-biaya.destroy', $pb) }}">
                                                @csrf @method('DELETE')
                                            </form>
                                            <button data-confirm-delete="Hapus pos biaya {{ $pb->nama }}?"
                                                data-form-id="del-pb-{{ $pb->id }}"
                                                class="btn-danger btn-sm">Hapus</button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Modal Edit --}}
                                <div id="modal-edit-{{ $pb->id }}" class="modal-backdrop hidden">
                                    <div class="modal-box">
                                        <div class="modal-header">
                                            <h3 class="font-semibold text-gray-900">Edit Pos Biaya</h3>
                                            <button data-modal-close="modal-edit-{{ $pb->id }}"
                                                class="text-gray-400 hover:text-gray-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <form method="POST" action="{{ route('master.pos-biaya.update', $pb) }}">
                                            @csrf @method('PUT')
                                            <div class="modal-body space-y-4">
                                                <div>
                                                    <label class="form-label">Nama Pos <span class="text-red-500">*</span></label>
                                                    <input type="text" name="nama" value="{{ $pb->nama }}"
                                                        class="form-input" required>
                                                </div>
                                                <div>
                                                    <label class="form-label">Anggaran</label>
                                                    <div class="relative">
                                                        <span class="absolute left-3 inset-y-0 flex items-center text-gray-500 text-sm">Rp</span>
                                                        <input type="number" name="anggaran" value="{{ $pb->anggaran }}"
                                                            class="form-input pl-9" min="0" step="1000">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="form-label">Keterangan</label>
                                                    <textarea name="keterangan" rows="2"
                                                        class="form-textarea">{{ $pb->keterangan }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" data-modal-close="modal-edit-{{ $pb->id }}" class="btn-secondary">Batal</button>
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
    @if($tahunAktif)
    <div id="modal-tambah" class="modal-backdrop hidden">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="font-semibold text-gray-900">Tambah Pos Biaya</h3>
                <button data-modal-close="modal-tambah" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('master.pos-biaya.store') }}">
                @csrf
                <input type="hidden" name="tahun_ajaran_id" value="{{ $tahunAktif->id }}">
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">Nama Pos <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}"
                            class="form-input" placeholder="Contoh: Pemeliharaan Gedung" required>
                        @error('nama')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Anggaran</label>
                        <div class="relative">
                            <span class="absolute left-3 inset-y-0 flex items-center text-gray-500 text-sm">Rp</span>
                            <input type="number" name="anggaran" value="{{ old('anggaran', 0) }}"
                                class="form-input pl-9" placeholder="0" min="0" step="1000">
                        </div>
                        @error('anggaran')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="form-textarea"
                            placeholder="Keterangan (opsional)">{{ old('keterangan') }}</textarea>
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
