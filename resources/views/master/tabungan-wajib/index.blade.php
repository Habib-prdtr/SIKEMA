<x-layouts.app title="Tabungan Wajib">
    <x-slot:pageTitle>Master Data / Tabungan Wajib</x-slot:pageTitle>

    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tarif Tabungan Wajib per Kelas</h1>
                <p class="text-gray-500 text-sm mt-0.5">Kelola tarif pembayaran Tabungan Wajib bulanan berdasarkan tingkat kelas</p>
            </div>
            @if($tahunAktif)
                <div class="flex gap-2">
                    @if($tabunganWajib->isEmpty() && $daftarTahunAjaran->isNotEmpty())
                        <button data-modal-open="modal-extract" class="btn-secondary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Extract dari Tahun Sebelumnya
                        </button>
                    @endif
                    <button data-modal-open="modal-tambah" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Tarif
                    </button>
                </div>
            @endif
        </div>

        @if(!$tahunAktif)
            <div class="alert-warning">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>Aktifkan tahun ajaran terlebih dahulu untuk mengelola tarif Tabungan Wajib.</p>
            </div>
        @endif

        <div class="card">
            @if($tabunganWajib->isEmpty())
                <div class="p-12 text-center text-gray-400">
                    <svg class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-sm">Belum ada tarif Tabungan Wajib yang didefinisikan untuk tahun ajaran ini.</p>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="w-16 text-center">#</th>
                                <th>Tingkat / Kelas</th>
                                <th class="text-right">Tarif Bulanan</th>
                                <th class="text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tabunganWajib as $i => $t)
                                <tr>
                                    <td class="text-center font-semibold text-gray-500">{{ $i + 1 }}</td>
                                    <td class="font-semibold text-gray-900">{{ $t->kelas }}</td>
                                    <td class="text-right font-bold text-emerald-700">
                                        {{ format_rupiah($t->tarif) }}
                                    </td>
                                    <td class="text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button class="btn-secondary btn-sm"
                                                data-modal-open="modal-edit-{{ $t->id }}">
                                                Edit
                                            </button>
                                            <form id="del-t-{{ $t->id }}" method="POST"
                                                action="{{ route('master.tabungan-wajib.destroy', $t) }}">
                                                @csrf @method('DELETE')
                                            </form>
                                            <button data-confirm-delete="Hapus tarif Tabungan Wajib kelas {{ $t->kelas }}?"
                                                data-form-id="del-t-{{ $t->id }}"
                                                class="btn-danger btn-sm">Hapus</button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Modal Edit per item --}}
                                <div id="modal-edit-{{ $t->id }}" class="modal-backdrop hidden">
                                    <div class="modal-box">
                                        <div class="modal-header">
                                            <h3 class="font-semibold text-gray-900">Edit Tarif Tabungan Wajib</h3>
                                            <button data-modal-close="modal-edit-{{ $t->id }}"
                                                class="text-gray-400 hover:text-gray-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <form method="POST" action="{{ route('master.tabungan-wajib.update', $t) }}">
                                            @csrf @method('PUT')
                                            <div class="modal-body space-y-4 text-left">
                                                <div>
                                                    <label class="form-label">Nama/Tingkat Kelas <span class="text-red-500">*</span></label>
                                                    <input type="text" name="kelas" value="{{ $t->kelas }}"
                                                        class="form-input" placeholder="Contoh: Kelas 7" maxlength="50" required>
                                                </div>
                                                <div>
                                                    <label class="form-label">Tarif Bulanan <span class="text-red-500">*</span></label>
                                                    <div class="relative">
                                                        <span class="absolute left-3 inset-y-0 flex items-center text-gray-500 text-sm">Rp</span>
                                                        <input type="number" name="tarif" value="{{ $t->tarif }}"
                                                            class="form-input pl-9" min="0" step="1000" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" data-modal-close="modal-edit-{{ $t->id }}" class="btn-secondary">Batal</button>
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
                <h3 class="font-semibold text-gray-900">Tambah Tarif Tabungan Wajib</h3>
                <button data-modal-close="modal-tambah" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('master.tabungan-wajib.store') }}">
                @csrf
                <input type="hidden" name="tahun_ajaran_id" value="{{ $tahunAktif->id }}">
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">Nama/Tingkat Kelas <span class="text-red-500">*</span></label>
                        <input type="text" name="kelas" value="{{ old('kelas') }}"
                            class="form-input" placeholder="Contoh: Kelas 7" maxlength="50" required>
                        @error('kelas')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Tarif Bulanan <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 inset-y-0 flex items-center text-gray-500 text-sm">Rp</span>
                            <input type="number" name="tarif" value="{{ old('tarif') }}"
                                class="form-input pl-9" placeholder="0" min="0" step="1000" required>
                        </div>
                        @error('tarif')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-modal-close="modal-tambah" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Extract --}}
    @if($tahunAktif && $tabunganWajib->isEmpty() && $tahunSebelumnya)
    <div id="modal-extract" class="modal-backdrop hidden">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="font-semibold text-gray-900">Konfirmasi Extract</h3>
                <button data-modal-close="modal-extract" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('master.tabungan-wajib.extract') }}">
                @csrf
                <div class="modal-body space-y-4">
                    <p class="text-sm text-gray-600">Apakah Anda yakin ingin melakukan extract tarif Tabungan Wajib dari tahun ajaran <strong>{{ $tahunSebelumnya->nama }}</strong>? Berikut data yang akan diekstrak:</p>
                    <div class="table-wrapper text-sm">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Kelas</th>
                                    <th class="text-right">Tarif</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tabunganWajibSebelumnya as $t)
                                    <tr>
                                        <td>{{ $t->kelas }}</td>
                                        <td class="text-right">{{ format_rupiah($t->tarif) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-modal-close="modal-extract" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Yakin, Extract</button>
                </div>
            </form>
        </div>
    </div>
    @endif
    @endif

</x-layouts.app>
