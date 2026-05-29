<x-layouts.app title="Saldo Awal Kas">
    <x-slot:pageTitle>Master Data / Saldo Awal</x-slot:pageTitle>

    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Saldo Awal Kas</h1>
                <p class="text-gray-500 text-sm mt-0.5">Atur saldo awal kas per tahun ajaran</p>
            </div>
            <button data-modal-open="modal-tambah" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Set Saldo Awal
            </button>
        </div>

        <div class="card">
            @if($saldoList->isEmpty())
                <div class="p-12 text-center text-gray-400">
                    <svg class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <p class="text-sm">Belum ada data saldo awal.</p>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tahun Ajaran</th>
                                <th class="text-right">Saldo Awal (Rp)</th>
                                <th>Keterangan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($saldoList as $saldo)
                                <tr>
                                    <td class="font-semibold text-gray-900">
                                        {{ $saldo->tahunAjaran->nama }}
                                        @if($saldo->tahunAjaran->is_aktif)
                                            <span class="badge-green ml-1">Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-right font-bold text-emerald-700 text-base">
                                        Rp {{ number_format($saldo->jumlah, 0, ',', '.') }}
                                    </td>
                                    <td class="text-gray-500 text-sm">{{ $saldo->keterangan ?? '-' }}</td>
                                    <td class="text-center">
                                        <button class="btn-secondary btn-sm"
                                            data-modal-open="modal-edit-{{ $saldo->id }}">Edit</button>
                                    </td>
                                </tr>

                                {{-- Modal Edit --}}
                                <div id="modal-edit-{{ $saldo->id }}" class="modal-backdrop hidden">
                                    <div class="modal-box">
                                        <div class="modal-header">
                                            <h3 class="font-semibold text-gray-900">Edit Saldo Awal — TA {{ $saldo->tahunAjaran->nama }}</h3>
                                            <button data-modal-close="modal-edit-{{ $saldo->id }}"
                                                class="text-gray-400 hover:text-gray-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <form method="POST" action="{{ route('master.saldo-awal.update', $saldo) }}">
                                            @csrf @method('PUT')
                                            <div class="modal-body space-y-4">
                                                <div>
                                                    <label class="form-label">Jumlah Saldo Awal</label>
                                                    <div class="relative">
                                                        <span class="absolute left-3 inset-y-0 flex items-center text-gray-500 text-sm">Rp</span>
                                                        <input type="number" name="jumlah" value="{{ $saldo->jumlah }}"
                                                            class="form-input pl-9" min="0" step="1000" required>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="form-label">Keterangan</label>
                                                    <textarea name="keterangan" rows="2"
                                                        class="form-textarea">{{ $saldo->keterangan }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" data-modal-close="modal-edit-{{ $saldo->id }}" class="btn-secondary">Batal</button>
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
    <div id="modal-tambah" class="modal-backdrop hidden">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="font-semibold text-gray-900">Set Saldo Awal Kas</h3>
                <button data-modal-close="modal-tambah" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('master.saldo-awal.store') }}">
                @csrf
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">Tahun Ajaran <span class="text-red-500">*</span></label>
                        <select name="tahun_ajaran_id" class="form-select" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach($tahunList as $ta)
                                <option value="{{ $ta->id }}" {{ old('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama }} {{ $ta->is_aktif ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('tahun_ajaran_id')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Jumlah Saldo Awal <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 inset-y-0 flex items-center text-gray-500 text-sm">Rp</span>
                            <input type="number" name="jumlah" value="{{ old('jumlah', 0) }}"
                                class="form-input pl-9" min="0" step="1000" required>
                        </div>
                        @error('jumlah')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="form-textarea"
                            placeholder="Opsional">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-modal-close="modal-tambah" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>
