<x-layouts.app title="Saldo Kas">
    <x-slot:pageTitle>Master Data / Saldo Kas</x-slot:pageTitle>

    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Saldo Kas & Alur Kas</h1>
                <p class="text-gray-500 text-sm mt-0.5">
                    @if($tahunAktif)
                        Tahun Ajaran aktif saat ini: <span class="font-semibold text-emerald-600">{{ $tahunAktif->nama }}</span>
                    @else
                        <span class="text-amber-600 font-medium">(belum ada tahun ajaran aktif)</span>
                    @endif
                </p>
            </div>
        </div>

        @if(!$tahunAktif)
            <div class="card p-6 text-center text-gray-500">
                <svg class="w-14 h-14 mx-auto mb-3 text-amber-500 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <h3 class="text-lg font-bold text-gray-800">Tahun Ajaran Belum Aktif</h3>
                <p class="text-sm mt-1 max-w-md mx-auto">Silakan pilih dan aktifkan tahun ajaran terlebih dahulu di menu Tahun Ajaran untuk dapat mengelola saldo kas.</p>
                <a href="{{ route('master.tahun-ajaran.index') }}" class="btn-primary mt-4 inline-flex">Ke Tahun Ajaran</a>
            </div>
        @elseif(!$saldoAwal)
            <div class="max-w-md mx-auto my-10">
                <div class="card p-6 shadow-md border border-gray-100 space-y-4 bg-white">
                    <div class="text-center space-y-1.5">
                        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">Inisialisasi Saldo Kas</h2>
                        <p class="text-sm text-gray-500">Masukkan saldo kas saat ini untuk memulai transaksi di Tahun Ajaran <strong>{{ $tahunAktif->nama }}</strong></p>
                    </div>
                    
                    <form method="POST" action="{{ route('master.saldo-awal.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="tahun_ajaran_id" value="{{ $tahunAktif->id }}">
                        
                        <div>
                            <label class="form-label font-medium text-gray-700">Jumlah Saldo Awal <span class="text-red-500">*</span></label>
                            <div class="relative mt-1">
                                <span class="absolute left-3 inset-y-0 flex items-center text-gray-500 text-sm font-semibold">Rp</span>
                                <input type="number" name="jumlah" value="0" 
                                    class="form-input pl-9 w-full font-bold text-gray-800 text-lg" 
                                    min="0" step="1000" required autofocus>
                            </div>
                            @error('jumlah')<p class="form-error text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label class="form-label font-medium text-gray-700">Keterangan</label>
                            <textarea name="keterangan" rows="2" class="form-textarea mt-1 w-full text-sm" 
                                placeholder="Contoh: Saldo pembukaan awal tahun ajaran"></textarea>
                            @error('keterangan')<p class="form-error text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        <button type="submit" class="btn-primary w-full justify-center text-base py-2.5 mt-2">
                            Simpan & Inisialisasi Saldo
                        </button>
                    </form>
                </div>
            </div>
        @else
            <!-- Dashboard Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Saldo Kas Saat Ini -->
                <div class="card p-4 bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md border-0 flex flex-col justify-between">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider opacity-85">Saldo Kas Saat Ini</div>
                        <div class="text-2xl font-extrabold mt-1.5 tracking-tight">{{ format_rupiah($saldoSaatIni) }}</div>
                    </div>
                    <div class="text-xs mt-3 opacity-75 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Tahun Ajaran: {{ $tahunAktif->nama }}</span>
                    </div>
                </div>

                <!-- Saldo Awal -->
                <div class="card p-4 border border-gray-100 bg-white shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Saldo Awal Kas</div>
                        <div class="text-xl font-bold text-gray-800 mt-1.5">{{ format_rupiah($saldoAwal->jumlah) }}</div>
                    </div>
                    <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-50">
                        <span class="text-xs text-gray-400">Pencatatan Pertama</span>
                        <button data-modal-open="modal-edit-{{ $saldoAwal->id }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 underline cursor-pointer">
                            Ubah
                        </button>
                    </div>
                </div>

                <!-- Total Pemasukan -->
                <div class="card p-4 border border-gray-100 bg-white shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Pemasukan</div>
                        <div class="text-xl font-bold text-emerald-600 mt-1.5">+{{ format_rupiah($totalPemasukan) }}</div>
                    </div>
                    <div class="text-xs text-gray-400 mt-3 pt-2 border-t border-gray-50">
                        Dari transaksi penerimaan siswa
                    </div>
                </div>

                <!-- Total Pengeluaran -->
                <div class="card p-4 border border-gray-100 bg-white shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Pengeluaran</div>
                        <div class="text-xl font-bold text-amber-600 mt-1.5">-{{ format_rupiah($totalPengeluaran) }}</div>
                    </div>
                    <div class="text-xs text-gray-400 mt-3 pt-2 border-t border-gray-50">
                        Realisasi pos biaya operasional
                    </div>
                </div>
            </div>

            <!-- Alur Kas / Riwayat Transaksi -->
            <div class="card">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">Alur Kas & Riwayat Transaksi</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Urutan alur kas terhitung secara kronologis berdasarkan mutasi debet/kredit</p>
                    </div>
                    <span class="text-xs text-gray-500 bg-gray-50 border border-gray-150 px-3 py-1 rounded-full font-medium">
                        {{ count($records) }} Rekor Kas
                    </span>
                </div>

                @if($records->isEmpty())
                    <div class="p-12 text-center text-gray-400">
                        <svg class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm">Belum ada aktivitas transaksi kas.</p>
                    </div>
                @else
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Keterangan</th>
                                    <th class="text-right">Debit (Pemasukan)</th>
                                    <th class="text-right">Kredit (Pengeluaran)</th>
                                    <th class="text-right">Saldo Kas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($records as $record)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="text-sm font-medium text-gray-600 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($record->tanggal)->translatedFormat('d M Y') }}
                                        </td>
                                        <td>
                                            @if($record->jenis === 'saldo_awal')
                                                <span class="badge-blue">Saldo Awal</span>
                                            @elseif($record->jenis === 'pemasukan')
                                                <span class="badge-green">Pemasukan</span>
                                            @else
                                                <span class="badge-yellow">Pengeluaran</span>
                                            @endif
                                        </td>
                                        <td class="text-sm text-gray-700 max-w-xs md:max-w-md truncate" title="{{ $record->keterangan }}">
                                            {{ $record->keterangan }}
                                        </td>
                                        <td class="text-right font-medium text-emerald-600 whitespace-nowrap">
                                            @if($record->debit > 0)
                                                +{{ format_rupiah($record->debit) }}
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        </td>
                                        <td class="text-right font-medium text-amber-600 whitespace-nowrap">
                                            @if($record->kredit > 0)
                                                -{{ format_rupiah($record->kredit) }}
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        </td>
                                        <td class="text-right font-bold text-gray-900 whitespace-nowrap">
                                            {{ format_rupiah($record->running_saldo) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Modal Edit Saldo Awal --}}
            <div id="modal-edit-{{ $saldoAwal->id }}" class="modal-backdrop hidden">
                <div class="modal-box">
                    <div class="modal-header">
                        <h3 class="font-semibold text-gray-900">Ubah Saldo Awal Kas</h3>
                        <button data-modal-close="modal-edit-{{ $saldoAwal->id }}" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('master.saldo-awal.update', $saldoAwal) }}">
                        @csrf @method('PUT')
                        <div class="modal-body space-y-4">
                            <p class="text-xs text-amber-600 bg-amber-50 border border-amber-100 p-2.5 rounded-md">
                                <strong>Catatan:</strong> Mengubah nilai saldo awal akan otomatis memengaruhi perhitungan "Saldo Kas Saat Ini" serta running saldo pada seluruh alur kas.
                            </p>
                            <div>
                                <label class="form-label font-medium text-gray-700">Jumlah Saldo Awal</label>
                                <div class="relative mt-1">
                                    <span class="absolute left-3 inset-y-0 flex items-center text-gray-500 text-sm font-semibold">Rp</span>
                                    <input type="number" name="jumlah" value="{{ $saldoAwal->jumlah }}"
                                        class="form-input pl-9 w-full font-bold text-gray-800 text-lg" min="0" step="1000" required>
                                </div>
                            </div>
                            <div>
                                <label class="form-label font-medium text-gray-700">Keterangan</label>
                                <textarea name="keterangan" rows="2" class="form-textarea mt-1 w-full text-sm">{{ $saldoAwal->keterangan }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-modal-close="modal-edit-{{ $saldoAwal->id }}" class="btn-secondary">Batal</button>
                            <button type="submit" class="btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
