<x-layouts.app title="Dashboard">
    <x-slot:pageTitle>Dashboard</x-slot:pageTitle>

    <div class="space-y-6">

        {{-- Heading --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-500 text-sm mt-0.5">
                Selamat datang, {{ auth()->user()->name }}
                @if ($tahunAktif)
                    — Tahun Ajaran <span class="font-semibold text-emerald-600">{{ $tahunAktif->nama }}</span>
                @endif
            </p>
        </div>

        {{-- ── Banners / Alerts ────────────────────────────────── --}}
        @if (!$tahunAktif)
            <div class="alert-warning">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-medium">Tahun Ajaran Belum Aktif</p>
                    <p class="text-sm mt-0.5">Silakan aktifkan tahun ajaran terlebih dahulu di menu
                        <a href="{{ route('master.tahun-ajaran.index') }}" class="font-semibold underline">Master Data →
                            Tahun Ajaran</a>.
                    </p>
                </div>
            </div>
        @endif

        @if ($sppBelumLunas > 0)
            <div class="alert-info">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p>
                    <strong>{{ $sppBelumLunas }} siswa</strong> belum melunasi SPP bulan
                    {{ now()->locale('id')->isoFormat('MMMM YYYY') }}.
                </p>
            </div>
        @endif

        {{-- ── Main Grid: Left Search, Right Cards ──────────────── --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-start">
            {{-- Left Column: Quick Student Search --}}
            <div class="md:col-span-2 space-y-4">
                @if ($tahunAktif)
                    <div class="card p-5 bg-white rounded-xl shadow-sm border border-gray-200 space-y-4">
                        <div
                            class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 pb-4">
                            <div class="space-y-1">
                                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Pencatatan Penerimaan Cepat
                                </h3>
                                <p class="text-gray-500 text-xs">Cari nama, kelas, atau nomor induk siswa untuk langsung
                                    mencatat pembayaran.</p>
                            </div>
                            <form method="GET" action="{{ route('dashboard') }}"
                                class="flex gap-2 w-full md:max-w-md">
                                <div class="relative flex-1">
                                    <span class="absolute left-3 inset-y-0 flex items-center text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </span>
                                    <input type="text" name="cari" value="{{ request('cari') }}"
                                        class="form-input pl-9 w-full text-gray-900"
                                        placeholder="Ketik nama, no. induk, atau kelas...">
                                </div>
                                <button type="submit" class="btn-primary shrink-0">
                                    Cari
                                </button>
                                @if (request('cari'))
                                    <a href="{{ route('dashboard') }}" class="btn-secondary flex items-center">
                                        Reset
                                    </a>
                                @endif
                            </form>
                        </div>

                        @if ($daftarSiswa && $daftarSiswa->isNotEmpty())
                            <div class="table-wrapper">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>No. Induk</th>
                                            <th>Nama Siswa</th>
                                            <th>Kelas</th>
                                            <th class="text-center w-24">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($daftarSiswa as $s)
                                            @php
                                                $isTerpilih = isset($siswa) && $siswa->id === $s->id;
                                            @endphp
                                            <tr class="{{ $isTerpilih ? 'bg-emerald-50/50' : '' }}">
                                                <td class="font-mono text-xs font-medium text-gray-600">
                                                    {{ $s->siswa->no_induk }}</td>
                                                <td class="font-medium text-gray-900">
                                                    <div class="flex items-center gap-2">
                                                        <span>{{ $s->siswa->nama }}</span>
                                                        @if ($isTerpilih)
                                                            <span class="badge-green text-xs">
                                                                Terpilih
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>{{ $s->siswa->kelas }}</td>
                                                <td class="text-center">
                                                    @if ($isTerpilih)
                                                        <button type="button"
                                                            class="btn-secondary btn-sm opacity-60 cursor-not-allowed w-full"
                                                            disabled>
                                                            Terpilih
                                                        </button>
                                                    @else
                                                        <a href="{{ route('dashboard', ['no_induk' => $s->siswa->no_induk, 'cari' => request('cari')]) }}"
                                                            class="btn-primary btn-sm w-full block text-center">
                                                            Catat
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            @if ($daftarSiswa->hasPages())
                                <div class="pt-2">
                                    {{ $daftarSiswa->links() }}
                                </div>
                            @endif
                        @else
                            <div
                                class="p-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="text-sm font-medium">Tidak ada data siswa ditemukan.</p>
                                @if ($tahunAktif)
                                    <p class="text-xs text-gray-400 mt-1">Pastikan siswa sudah diaktifkan di Tahun
                                        Ajaran {{ $tahunAktif->nama }}.</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif

                @isset($siswa)
                    {{-- STEP 2: Info Siswa --}}
                    <div class="card animate-pulse-once">
                        <div class="card-header">
                            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                                <span
                                    class="w-6 h-6 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                                Data Siswa
                            </h3>
                            <span class="badge-green">Ditemukan</span>
                        </div>
                        <div class="card-body">
                            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center shrink-0">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-2 text-sm flex-1">
                                        <div>
                                            <p class="text-gray-500">Nama</p>
                                            <p class="font-semibold text-gray-900">{{ $siswa->siswa->nama }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">No. Induk</p>
                                            <p class="font-mono font-semibold">{{ $siswa->siswa->no_induk }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Kelas</p>
                                            <p class="font-semibold">{{ $siswa->siswa->kelas }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Tahun Ajaran</p>
                                            <p class="font-semibold">{{ $siswa->tahunAjaran->nama }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Tarif SPP</p>
                                            <p class="font-semibold text-emerald-700">
                                                {{ format_rupiah($siswa->tarif_spp) }}</p>
                                        </div>
                                        @if ($siswa->tunggakan_awal > 0)
                                            <div>
                                                <p class="text-gray-500">Sisa Tunggakan</p>
                                                <p class="font-semibold text-amber-700">
                                                    {{ format_rupiah($sisaTunggakan) }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if ($siswa->tunggakan_awal > 0 && $sisaTunggakan > 0)
                                <div class="alert-warning mt-4">
                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p>Terdapat <strong>tunggakan tahun sebelumnya</strong> sebesar
                                        <strong>{{ format_rupiah($sisaTunggakan) }}</strong>.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- STEP 3: Form Pembayaran --}}
                    <div class="card">
                        <div class="card-header">
                            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                                <span
                                    class="w-6 h-6 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                                Pilih Pembayaran
                            </h3>
                        </div>
                        <form id="form-penerimaan" method="POST" action="{{ route('penerimaan.store') }}">
                            @csrf
                            <input type="hidden" name="siswa_tahun_ajaran_id" value="{{ $siswa->id }}">
                            <input type="hidden" id="total-bayar-input" name="total_bayar" value="0">

                            <div class="card-body space-y-5">
                                @php
                                    $defaultTab = 'spp';
                                @endphp

                                {{-- Tab Navigation (Boxed Segmented Selector) --}}
                                <div class="grid grid-cols-2 gap-3 mb-2">
                                    <button type="button" onclick="switchTab('spp')" id="tab-btn-spp"
                                        class="tab-btn p-3 rounded-xl border-2 text-center transition-all focus:outline-none flex-1
                                    border-gray-200 bg-white text-gray-700 font-medium hover:border-gray-300 hover:bg-gray-50">
                                        <span class="block text-base">SPP Bulanan</span>
                                        <span class="tab-desc block text-xs font-normal text-gray-500 mt-0.5">Daftar SPP
                                            per bulan</span>
                                    </button>
                                    <button type="button" onclick="switchTab('iuran')" id="tab-btn-iuran"
                                        class="tab-btn p-3 rounded-xl border-2 text-center transition-all focus:outline-none flex-1
                                    border-gray-200 bg-white text-gray-700 font-medium hover:border-gray-300 hover:bg-gray-50">
                                        <span class="block text-base">Iuran / Lainnya</span>
                                        <span class="tab-desc block text-xs font-normal text-gray-500 mt-0.5">Iuran &
                                            tunggakan lain</span>
                                    </button>
                                </div>

                                {{-- SPP Section --}}
                                <div id="section-spp" class="space-y-3 hidden">
                                    @if ($tagihanSpp->isNotEmpty())
                                        <div class="space-y-2">
                                            @foreach ($tagihanSpp as $spp)
                                                @php
                                                    $lunas = $spp->status === 'lunas';
                                                    $nama = \Carbon\Carbon::createFromDate($spp->tahun, $spp->bulan, 1)
                                                        ->locale('id')
                                                        ->isoFormat('MMMM YYYY');
                                                    $nominal = $spp->status === 'lunas' ? $spp->tagihan : $spp->sisa();

                                                    // Check if the SPP month has passed (is in the past relative to current year and month)
                                                    $isTerlewat =
                                                        !$lunas &&
                                                        ($spp->tahun < now()->year ||
                                                            ($spp->tahun == now()->year && $spp->bulan < now()->month));
                                                @endphp
                                                <label
                                                    class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-all
                                                {{ $lunas ? 'border-gray-200 bg-gray-50 opacity-60 cursor-not-allowed' : ($isTerlewat ? 'border-red-200 bg-red-50/30 hover:border-red-400 hover:bg-red-50' : 'border-gray-200 hover:border-emerald-400 hover:bg-emerald-50') }}">
                                                    <div class="flex items-center gap-3">
                                                        <input type="checkbox" name="items[spp][]"
                                                            value="{{ $spp->id }}"
                                                            data-tagihan-nominal="{{ $nominal }}"
                                                            {{ $lunas ? 'disabled' : '' }}
                                                            class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-900">SPP
                                                                {{ $nama }}</p>
                                                            <p
                                                                class="text-xs {{ $isTerlewat ? 'text-red-600 font-medium' : 'text-gray-500' }}">
                                                                {{ $spp->status === 'cicilan' ? 'Cicilan — Sisa bayar' : ($lunas ? 'Sudah lunas' : ($isTerlewat ? 'Terlewat / Belum dibayar' : 'Belum dibayar')) }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <p
                                                            class="font-semibold text-sm {{ $isTerlewat ? 'text-red-700' : '' }}">
                                                            {{ format_rupiah($nominal) }}</p>
                                                        @if ($lunas)
                                                            <span class="badge-green text-xs">Lunas</span>
                                                        @elseif($isTerlewat)
                                                            <span class="badge-red text-xs">Terlewat</span>
                                                        @else
                                                            <span class="badge-yellow text-xs">Belum Bayar</span>
                                                        @endif
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <div
                                            class="p-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                            <p class="text-sm font-medium">Tidak ada tagihan SPP untuk siswa ini.</p>
                                        </div>
                                    @endif
                                </div>

                                {{-- Iuran & Lainnya Section --}}
                                <div id="section-iuran" class="space-y-4 hidden">
                                    {{-- Iuran --}}
                                    @if ($tagihanIuran->isNotEmpty())
                                        <div class="space-y-2">
                                            @foreach ($tagihanIuran as $iuran)
                                                @php
                                                    $lunas = $iuran->status === 'lunas';
                                                    $nominal = $lunas ? $iuran->tagihan : $iuran->sisa();
                                                @endphp
                                                <label
                                                    class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-all
                                                {{ $lunas ? 'border-gray-200 bg-gray-50 opacity-60 cursor-not-allowed' : 'border-gray-200 hover:border-emerald-400 hover:bg-emerald-50' }}">
                                                    <div class="flex items-center gap-3">
                                                        <input type="checkbox" name="items[iuran][]"
                                                            value="{{ $iuran->id }}"
                                                            data-tagihan-nominal="{{ $nominal }}"
                                                            {{ $lunas ? 'disabled' : '' }}
                                                            class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                                        <label class="flex-1 cursor-pointer">
                                                            <span
                                                                class="text-sm font-medium text-gray-900 block">{{ $iuran->jenisPenerimaan->nama }}</span>
                                                            <span class="text-xs text-gray-500 block">
                                                                {{ $iuran->status === 'cicilan' ? 'Cicilan — Sisa bayar' : ($lunas ? 'Sudah lunas' : 'Belum dibayar') }}
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="font-semibold text-sm">{{ format_rupiah($nominal) }}</p>
                                                        @if ($lunas)
                                                            <span class="badge-green text-xs">Lunas</span>
                                                        @endif
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Tunggakan --}}
                                    @if ($sisaTunggakan > 0)
                                        <div
                                            class="p-3 rounded-lg border border-amber-200 bg-amber-50 flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <input type="checkbox" name="items[tunggakan]" value="1"
                                                    data-tagihan-nominal="{{ $sisaTunggakan }}"
                                                    class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">Cicil/Lunasi Tunggakan</p>
                                                    <p class="text-xs text-amber-700">Sisa:
                                                        {{ format_rupiah($sisaTunggakan) }}</p>
                                                </div>
                                            </div>
                                            <div>
                                                <input type="number" name="nominal_tunggakan"
                                                    value="{{ $sisaTunggakan }}"
                                                    class="form-input w-36 text-right text-sm" min="1000"
                                                    max="{{ $sisaTunggakan }}" step="1000"
                                                    placeholder="Nominal cicil">
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Jika Keduanya Kosong --}}
                                    @if ($tagihanIuran->isEmpty() && $sisaTunggakan == 0)
                                        <div
                                            class="p-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-sm font-medium">Tidak ada tagihan iuran atau tunggakan.</p>
                                        </div>
                                    @endif
                                </div>

                                {{-- Element dummy untuk mencegah error JS --}}
                                <div id="section-tunggakan" class="hidden"></div>

                                {{-- Total, Info, & Catatan (Hidden on load) --}}
                                <div id="section-checkout" class="space-y-5 hidden">
                                    <div class="bg-gray-50 rounded-xl p-4 flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-gray-500">Total Pembayaran</p>
                                            <p id="total-bayar-display" class="text-2xl font-bold text-emerald-700">Rp 0
                                            </p>
                                        </div>
                                        <div class="text-right text-xs text-gray-400">
                                            <p>Tanggal: {{ now()->format('d/m/Y') }}</p>
                                            <p>Operator: {{ auth()->user()->name }}</p>
                                        </div>
                                    </div>

                                    {{-- Catatan --}}
                                    <div>
                                        <label class="form-label">Catatan (opsional)</label>
                                        <textarea name="catatan" rows="2" class="form-textarea" placeholder="Catatan tambahan...">{{ old('catatan') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons Footer (Hidden on load) --}}
                            <div id="section-actions"
                                class="px-6 py-4 border-t border-gray-100 flex items-center gap-3 hidden">
                                <button type="submit" class="btn-primary"
                                    onclick="return document.getElementById('total-bayar-input').value > 0 || (alert('Pilih minimal 1 item pembayaran'), false)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Proses Pembayaran
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                @endisset
            </div>

            {{-- Right Column: Stats Cards --}}
            <div class="md:col-span-1 space-y-4">
                {{-- Saldo Kas --}}
                <div class="card p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Saldo Kas</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ format_rupiah($totalSaldo) }}</p>
                            <p class="text-xs text-gray-400 mt-1">TA {{ $tahunAktif?->nama ?? '-' }}</p>
                        </div>
                        <div class="w-11 h-11 bg-emerald-100 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Penerimaan Bulan Ini --}}
                <div class="card p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Penerimaan Bulan Ini
                            </p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                {{ format_rupiah($totalPenerimaanBulanIni) }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ now()->locale('id')->isoFormat('MMMM YYYY') }}
                            </p>
                        </div>
                        <div class="w-11 h-11 bg-green-100 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Pengeluaran Bulan Ini --}}
                <div class="card p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pengeluaran Bulan Ini
                            </p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">
                                {{ format_rupiah($totalPengeluaranBulanIni) }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ now()->locale('id')->isoFormat('MMMM YYYY') }}
                            </p>
                        </div>
                        <div class="w-11 h-11 bg-red-100 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 17H5m0 0V9m0 8l8-8 4 4 6-6" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Jumlah Siswa --}}
                <div class="card p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa Aktif</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($jumlahSiswa) }}</p>
                            <p class="text-xs text-gray-400 mt-1">Terdaftar tahun ini</p>
                        </div>
                        <div class="w-11 h-11 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Grafik (SVG Bar Chart sederhana) + Transaksi ───── --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

            {{-- Horizontal Bar Chart Penerimaan Per Jenis --}}
            <div class="card xl:col-span-2">
                <div class="card-header flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Grafik Penerimaan per Jenis Pembayaran</h3>
                    <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100">TA {{ $tahunAktif?->nama ?? '-' }}</span>
                </div>
                <div class="p-5">
                    @if ($penerimaanPerJenisData->isNotEmpty())
                        @php
                            $itemCount = count($penerimaanPerJenisData);
                            $rowHeight = 36;
                            $topPadding = 15;
                            $bottomPadding = 40;
                            $svgHeight = $topPadding + ($itemCount * $rowHeight) + $bottomPadding;
                            $leftMargin = 130;
                            $rightMargin = 100;
                            $chartWidth = 720;
                            $barAreaWidth = $chartWidth - $leftMargin - $rightMargin;
                            $maxVal = max($maxPenerimaanJenis, 1);
                        @endphp
                        <div class="overflow-x-auto">
                            <svg viewBox="0 0 {{ $chartWidth }} {{ $svgHeight }}" class="w-full min-w-md">
                                {{-- Background Grid Lines & Scale Ticks --}}
                                @for ($k = 0; $k <= 4; $k++)
                                    @php
                                        $fraction = $k / 4;
                                        $gx = $leftMargin + ($fraction * $barAreaWidth);
                                        $gridVal = ($maxVal / 4) * $k;
                                    @endphp
                                    <line x1="{{ $gx }}" y1="{{ $topPadding - 5 }}" x2="{{ $gx }}"
                                        y2="{{ $svgHeight - $bottomPadding + 5 }}" stroke="#e5e7eb" stroke-dasharray="3,3"
                                        stroke-width="1" />
                                    <text x="{{ $gx }}" y="{{ $svgHeight - $bottomPadding + 22 }}"
                                        text-anchor="middle" font-size="11" fill="#6b7280">
                                        {{ number_format($gridVal, 0, ',', '.') }}
                                    </text>
                                @endfor

                                {{-- Bars & Labels --}}
                                @foreach ($penerimaanPerJenisData as $i => $item)
                                    @php
                                        $y = $topPadding + ($i * $rowHeight);
                                        $barY = $y + 5;
                                        $barHeight = 18;
                                        $val = $item['total'];
                                        $barW = $maxVal > 0 ? ($val / $maxVal) * $barAreaWidth : 0;
                                    @endphp

                                    {{-- Y-Axis Label --}}
                                    <text x="{{ $leftMargin - 12 }}" y="{{ $y + 18 }}" text-anchor="end"
                                        font-size="12" font-weight="500" fill="#374151">
                                        {{ $item['nama'] }}
                                    </text>

                                    @if ($val == 0)
                                        {{-- Zero Value Marker --}}
                                        <line x1="{{ $leftMargin }}" y1="{{ $y + 14 }}"
                                            x2="{{ $leftMargin + 15 }}" y2="{{ $y + 14 }}" stroke="#d1d5db"
                                            stroke-width="2" />
                                        <text x="{{ $leftMargin + 22 }}" y="{{ $y + 18 }}" font-size="11"
                                            font-weight="600" fill="#9ca3af">0</text>
                                    @else
                                        {{-- Bar Rect --}}
                                        <rect x="{{ $leftMargin }}" y="{{ $barY }}"
                                            width="{{ max($barW, 4) }}" height="{{ $barHeight }}" fill="#f59e0b"
                                            rx="4">
                                            <title>{{ $item['nama'] }}: {{ format_rupiah($val) }}</title>
                                        </rect>

                                        {{-- Value Text (Selalu di luar bar di sebelah kanan) --}}
                                        <text x="{{ $leftMargin + $barW + 8 }}" y="{{ $y + 18 }}"
                                            text-anchor="start" font-size="11" font-weight="bold" fill="#1f2937">
                                            {{ number_format($val, 0, ',', '.') }}
                                        </text>
                                    @endif
                                @endforeach
                            </svg>
                        </div>
                    @else
                        <div class="p-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <p class="text-sm font-medium">Belum ada data penerimaan untuk ditampilkan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Menu Cepat (Quick Actions) ──────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('penerimaan.catat') }}"
                class="card p-4 flex items-center gap-4 hover:border-emerald-300 hover:shadow-md transition-all group">
                <div
                    class="w-10 h-10 bg-emerald-100 group-hover:bg-emerald-200 rounded-xl flex items-center justify-center transition-colors shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-sm">Catat Penerimaan</p>
                    <p class="text-xs text-gray-500">Proses pembayaran siswa</p>
                </div>
            </a>
            <a href="{{ route('pengeluaran.catat') }}"
                class="card p-4 flex items-center gap-4 hover:border-red-200 hover:shadow-md transition-all group">
                <div
                    class="w-10 h-10 bg-red-100 group-hover:bg-red-200 rounded-xl flex items-center justify-center transition-colors shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-sm">Catat Pengeluaran</p>
                    <p class="text-xs text-gray-500">Rekam kas keluar</p>
                </div>
            </a>
            <a href="{{ route('laporan.penerimaan') }}"
                class="card p-4 flex items-center gap-4 hover:border-blue-200 hover:shadow-md transition-all group">
                <div
                    class="w-10 h-10 bg-blue-100 group-hover:bg-blue-200 rounded-xl flex items-center justify-center transition-colors shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-sm">Lihat Laporan</p>
                    <p class="text-xs text-gray-500">Rekap keuangan</p>
                </div>
            </a>
            <a href="{{ route('master.siswa.index') }}"
                class="card p-4 flex items-center gap-4 hover:border-purple-200 hover:shadow-md transition-all group">
                <div
                    class="w-10 h-10 bg-purple-100 group-hover:bg-purple-200 rounded-xl flex items-center justify-center transition-colors shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-sm">Data Siswa</p>
                    <p class="text-xs text-gray-500">Kelola data siswa</p>
                </div>
            </a>
        </div>

        {{-- ── Transaksi Terbaru ───────────────────────────────── --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">Transaksi Terbaru</h3>
                <a href="{{ route('penerimaan.index') }}"
                    class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    Lihat semua →
                </a>
            </div>
            @if ($transaksiTerbaru->isEmpty())
                <div class="p-10 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="text-sm">Belum ada transaksi</p>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No. Transaksi</th>
                                <th>Siswa</th>
                                <th>Kelas</th>
                                <th>Tanggal</th>
                                <th class="text-right">Jumlah</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaksiTerbaru as $trx)
                                <tr>
                                    <td class="font-mono text-xs">{{ $trx->no_transaksi }}</td>
                                    <td class="font-medium text-gray-900">{{ $trx->siswaTahunAjaran->siswa->nama }}
                                    </td>
                                    <td>{{ $trx->siswaTahunAjaran->siswa->kelas }}</td>
                                    <td>{{ $trx->tanggal->format('d/m/Y') }}</td>
                                    <td class="text-right font-semibold text-emerald-700">
                                        {{ format_rupiah($trx->total_bayar) }}
                                    </td>
                                    <td>
                                        <a href="{{ route('penerimaan.show', $trx) }}"
                                            class="text-xs text-blue-600 hover:text-blue-700 font-medium">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

    {{-- Script Tab Switcher --}}
    <script>
        function switchTab(tabName) {
            // Hide all sections
            const sppSec = document.getElementById('section-spp');
            const iuranSec = document.getElementById('section-iuran');
            const tunggakanSec = document.getElementById('section-tunggakan');

            if (sppSec) sppSec.classList.add('hidden');
            if (iuranSec) iuranSec.classList.add('hidden');
            if (tunggakanSec) tunggakanSec.classList.add('hidden');

            // Show selected section
            const activeSec = document.getElementById('section-' + tabName);
            if (activeSec) activeSec.classList.remove('hidden');

            // Show checkout and action sections
            const checkoutSec = document.getElementById('section-checkout');
            const actionsSec = document.getElementById('section-actions');
            if (checkoutSec) checkoutSec.classList.remove('hidden');
            if (actionsSec) actionsSec.classList.remove('hidden');

            // Reset all tab button styles to inactive
            const sppBtn = document.getElementById('tab-btn-spp');
            const iuranBtn = document.getElementById('tab-btn-iuran');

            if (sppBtn) {
                sppBtn.className =
                    "tab-btn p-3 rounded-xl border-2 text-center transition-all focus:outline-none flex-1 border-gray-200 bg-white text-gray-700 font-medium hover:border-gray-300 hover:bg-gray-50";
                const desc = sppBtn.querySelector('.tab-desc');
                if (desc) desc.className = "tab-desc block text-xs font-normal text-gray-500 mt-0.5";
            }
            if (iuranBtn) {
                iuranBtn.className =
                    "tab-btn p-3 rounded-xl border-2 text-center transition-all focus:outline-none flex-1 border-gray-200 bg-white text-gray-700 font-medium hover:border-gray-300 hover:bg-gray-50";
                const desc = iuranBtn.querySelector('.tab-desc');
                if (desc) desc.className = "tab-desc block text-xs font-normal text-gray-500 mt-0.5";
            }

            // Set active tab button style
            const activeBtn = document.getElementById('tab-btn-' + tabName);
            if (activeBtn) {
                activeBtn.className =
                    "tab-btn p-3 rounded-xl border-2 text-center transition-all focus:outline-none border-emerald-600 bg-emerald-50 text-emerald-800 font-semibold shadow-sm flex-1";
                const desc = activeBtn.querySelector('.tab-desc');
                if (desc) desc.className = "tab-desc block text-xs font-normal text-emerald-600 mt-0.5";
            }
        }
    </script>
</x-layouts.app>
