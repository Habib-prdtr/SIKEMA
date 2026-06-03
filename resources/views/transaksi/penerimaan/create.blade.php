<x-layouts.app title="Catat Penerimaan">
    <x-slot:pageTitle>Penerimaan / Pencatatan</x-slot:pageTitle>

    <div class="space-y-5 max-w-3xl">

        <div class="flex items-center gap-4">
            <a href="{{ route('penerimaan.index') }}" class="btn-secondary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Pencatatan Penerimaan</h1>
        </div>

        {{-- STEP 1: Cari Siswa --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                    <span class="w-6 h-6 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                    Cari Siswa
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('penerimaan.catat') }}" class="flex gap-3 max-w-md">
                    <input type="text" name="no_induk" value="{{ request('no_induk') }}"
                        class="form-input flex-1" placeholder="Masukkan No. Induk Siswa"
                        autofocus required>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Cari
                    </button>
                </form>
                @if(request('no_induk') && !isset($siswa))
                    <div class="alert-error mt-4 max-w-md">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>Siswa dengan No. Induk <strong>{{ request('no_induk') }}</strong> tidak ditemukan atau belum aktif di tahun ajaran ini.</p>
                    </div>
                @endif
            </div>
        </div>

        @isset($siswa)
        {{-- STEP 2: Info Siswa --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                    <span class="w-6 h-6 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                    Data Siswa
                </h3>
                <span class="badge-green">Ditemukan</span>
            </div>
            <div class="card-body">
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-2 text-sm flex-1">
                            <div><p class="text-gray-500">Nama</p><p class="font-semibold text-gray-900">{{ $siswa->siswa->nama }}</p></div>
                            <div><p class="text-gray-500">No. Induk</p><p class="font-mono font-semibold">{{ $siswa->siswa->no_induk }}</p></div>
                            <div><p class="text-gray-500">Kelas</p><p class="font-semibold">{{ $siswa->siswa->kelas }}</p></div>
                            <div><p class="text-gray-500">Tahun Ajaran</p><p class="font-semibold">{{ $siswa->tahunAjaran->nama }}</p></div>
                            <div><p class="text-gray-500">Tarif SPP</p><p class="font-semibold text-emerald-700">{{ format_rupiah($siswa->tarif_spp) }}</p></div>
                            @if($siswa->tunggakan_awal > 0)
                                <div>
                                    <p class="text-gray-500">Sisa Tunggakan</p>
                                    <p class="font-semibold text-amber-700">{{ format_rupiah($sisaTunggakan) }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($siswa->tunggakan_awal > 0 && $sisaTunggakan > 0)
                    <div class="alert-warning mt-4">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>Terdapat <strong>tunggakan tahun sebelumnya</strong> sebesar
                            <strong>{{ format_rupiah($sisaTunggakan) }}</strong>.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- STEP 3: Form Pembayaran --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                    <span class="w-6 h-6 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                    Pilih Pembayaran
                </h3>
            </div>
            <form id="form-penerimaan" method="POST" action="{{ route('penerimaan.store') }}">
                @csrf
                <input type="hidden" name="siswa_tahun_ajaran_id" value="{{ $siswa->id }}">
                <input type="hidden" id="total-bayar-input" name="total_bayar" value="0">

                <div class="card-body space-y-5">

                    {{-- SPP --}}
                    @if($tagihanSpp->isNotEmpty())
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="badge-blue">SPP Bulanan</span>
                        </h4>
                        <div class="space-y-2">
                            @foreach($tagihanSpp as $spp)
                                @php
                                    $lunas   = $spp->status === 'lunas';
                                    $nama    = \Carbon\Carbon::createFromDate($spp->tahun, $spp->bulan, 1)->locale('id')->isoFormat('MMMM YYYY');
                                    $nominal = $siswa->tarif_spp;
                                @endphp
                                <label class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-all
                                    {{ $lunas ? 'border-gray-200 bg-gray-50 opacity-60 cursor-not-allowed' : 'border-gray-200 hover:border-emerald-400 hover:bg-emerald-50' }}">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="items[spp][]" value="{{ $spp->id }}"
                                            data-tagihan-nominal="{{ $nominal }}"
                                            {{ $lunas ? 'disabled' : '' }}
                                            class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">SPP {{ $nama }}</p>
                                            <p class="text-xs text-gray-500">
                                                {{ $spp->status === 'cicilan' ? 'Cicilan — Sisa bayar' : ($lunas ? 'Sudah lunas' : 'Belum dibayar') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-sm">{{ format_rupiah($nominal) }}</p>
                                        @if($lunas) <span class="badge-green text-xs">Lunas</span> @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Iuran --}}
                    @if($tagihanIuran->isNotEmpty())
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="badge-yellow">Iuran</span>
                        </h4>
                        <div class="space-y-2">
                            @foreach($tagihanIuran as $iuran)
                                @php $lunas = $iuran->status === 'lunas'; @endphp
                                <label class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-all
                                    {{ $lunas ? 'border-gray-200 bg-gray-50 opacity-60 cursor-not-allowed' : 'border-gray-200 hover:border-emerald-400 hover:bg-emerald-50' }}">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="items[iuran][]" value="{{ $iuran->id }}"
                                            data-tagihan-nominal="{{ $iuran->jenisPenerimaan->nominal }}"
                                            {{ $lunas ? 'disabled' : '' }}
                                            class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $iuran->jenisPenerimaan->nama }}</p>
                                            <p class="text-xs text-gray-500">{{ $lunas ? 'Sudah lunas' : 'Belum dibayar' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-sm">{{ format_rupiah($iuran->jenisPenerimaan->nominal) }}</p>
                                        @if($lunas) <span class="badge-green text-xs">Lunas</span> @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Tunggakan --}}
                    @if($sisaTunggakan > 0)
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="badge-red">Tunggakan</span>
                        </h4>
                        <div class="p-3 rounded-lg border border-amber-200 bg-amber-50 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="items[tunggakan]" value="1"
                                    data-tagihan-nominal="{{ $sisaTunggakan }}"
                                    class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Cicil/Lunasi Tunggakan</p>
                                    <p class="text-xs text-amber-700">Sisa: {{ format_rupiah($sisaTunggakan) }}</p>
                                </div>
                            </div>
                            <div>
                                <input type="number" name="nominal_tunggakan" value="{{ $sisaTunggakan }}"
                                    class="form-input w-36 text-right text-sm" min="1000" max="{{ $sisaTunggakan }}"
                                    step="1000" placeholder="Nominal cicil">
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Total & Info --}}
                    <div class="bg-gray-50 rounded-xl p-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Total Pembayaran</p>
                            <p id="total-bayar-display" class="text-2xl font-bold text-emerald-700">Rp 0</p>
                        </div>
                        <div class="text-right text-xs text-gray-400">
                            <p>Tanggal: {{ now()->format('d/m/Y') }}</p>
                            <p>Operator: {{ auth()->user()->name }}</p>
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="catatan" rows="2" class="form-textarea"
                            placeholder="Catatan tambahan...">{{ old('catatan') }}</textarea>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-3">
                    <button type="submit" class="btn-primary"
                        onclick="return document.getElementById('total-bayar-input').value > 0 || (alert('Pilih minimal 1 item pembayaran'), false)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Proses Pembayaran
                    </button>
                    <a href="{{ route('penerimaan.catat') }}" class="btn-secondary">Reset</a>
                </div>
            </form>
        </div>
        @endisset

    </div>
</x-layouts.app>
