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

                        <div id="dash-siswa-search-results">
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
                                            <tr data-no-induk="{{ $s->siswa->no_induk }}" class="siswa-row-dashboard {{ $isTerpilih ? 'bg-emerald-50/50' : '' }}">
                                                <td class="font-mono text-xs font-medium text-gray-600">
                                                    {{ $s->siswa->no_induk }}</td>
                                                <td class="font-medium text-gray-900">
                                                    <div class="flex items-center gap-2">
                                                        <span>{{ $s->siswa->nama }}</span>
                                                        <span class="badge-terpilih-dashboard badge-green text-xs {{ $isTerpilih ? '' : 'hidden' }}">
                                                            Terpilih
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>{{ $s->siswa->kelas }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('dashboard', ['no_induk' => $s->siswa->no_induk, 'cari' => request('cari')]) }}"
                                                        data-no-induk="{{ $s->siswa->no_induk }}"
                                                        class="btn-catat-dashboard btn-sm w-full block text-center transition-all {{ $isTerpilih ? 'bg-emerald-50 text-emerald-700 border-2 border-emerald-500 font-semibold' : 'bg-emerald-600 hover:bg-emerald-700 text-white font-medium shadow-sm' }}">
                                                        {{ $isTerpilih ? 'Terpilih' : 'Catat' }}
                                                    </a>
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
                    </div>
                @endif

                {{-- Container Detail Transaksi Dashboard --}}
                <div id="container-detail-transaksi-dashboard" class="space-y-5 {{ isset($siswa) ? '' : 'hidden' }}">
                    {{-- STEP 2: Info Siswa --}}
                    <div class="card" id="card-data-siswa-dashboard">
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
                                            <p id="dash-detail-nama" class="font-semibold text-gray-900">{{ $siswa->siswa->nama ?? '' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">No. Induk</p>
                                            <p id="dash-detail-no-induk" class="font-mono font-semibold">{{ $siswa->siswa->no_induk ?? '' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Kelas</p>
                                            <p id="dash-detail-kelas" class="font-semibold">{{ $siswa->siswa->kelas ?? '' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Tahun Ajaran</p>
                                            <p id="dash-detail-tahun-ajaran" class="font-semibold">{{ $siswa->tahunAjaran->nama ?? '' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Tarif SPP</p>
                                            <p id="dash-detail-tarif-spp" class="font-semibold text-emerald-700">
                                                {{ isset($siswa) ? format_rupiah($siswa->tarif_spp) : '' }}</p>
                                        </div>
                                        <div id="dash-detail-sisa-tunggakan-wrapper" class="{{ ($siswa->tunggakan_awal ?? 0) > 0 ? '' : 'hidden' }}">
                                            <p class="text-gray-500">Sisa Tunggakan</p>
                                            <p id="dash-detail-sisa-tunggakan" class="font-semibold text-amber-700">
                                                {{ format_rupiah($sisaTunggakan ?? 0) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="dash-alert-tunggakan" class="alert-warning mt-4 {{ (($siswa->tunggakan_awal ?? 0) > 0 && ($sisaTunggakan ?? 0) > 0) ? '' : 'hidden' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>Terdapat <strong>tunggakan tahun sebelumnya</strong> sebesar
                                    <strong id="dash-text-sisa-tunggakan-alert">{{ format_rupiah($sisaTunggakan ?? 0) }}</strong>.
                                </p>
                            </div>
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
                        <form id="form-penerimaan" novalidate method="POST" action="{{ route('penerimaan.store') }}">
                            @csrf
                            <input type="hidden" id="dash-input-siswa-tahun-ajaran-id" name="siswa_tahun_ajaran_id" value="{{ $siswa->id ?? '' }}">
                            <input type="hidden" id="total-bayar-input" name="total_bayar" value="0">

                            <div class="card-body space-y-5">
                                @if($errors->any())
                                    <div class="alert-error">
                                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <div>
                                            <p class="font-semibold text-xs mb-1">Gagal memproses transaksi:</p>
                                            <ul class="list-disc list-inside text-xs space-y-0.5">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif

                                {{-- Tab Navigation (Boxed Segmented Selector) --}}
                                <div class="grid grid-cols-3 gap-3 mb-2">
                                    <button type="button" onclick="switchTab('spp')" id="tab-btn-spp"
                                        class="tab-btn p-3 rounded-xl border-2 text-center transition-all focus:outline-none flex-1
                                    border-gray-200 bg-white text-gray-700 font-medium hover:border-gray-300 hover:bg-gray-50">
                                        <span class="block text-base">SPP Bulanan</span>
                                        <span class="tab-desc block text-xs font-normal text-gray-500 mt-0.5">Daftar SPP
                                            per bulan</span>
                                    </button>
                                    <button type="button" onclick="switchTab('tabungan_wajib')" id="tab-btn-tabungan_wajib"
                                        class="tab-btn p-3 rounded-xl border-2 text-center transition-all focus:outline-none flex-1
                                    border-gray-200 bg-white text-gray-700 font-medium hover:border-gray-300 hover:bg-gray-50">
                                        <span class="block text-base">Tabungan Wajib</span>
                                        <span class="tab-desc block text-xs font-normal text-gray-500 mt-0.5">Tabungan wajib</span>
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
                                    <div id="dash-list-items-spp" class="space-y-2">
                                        @if (isset($tagihanSpp) && $tagihanSpp->isNotEmpty())
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
                                                    $hasDispensasi = isset($siswa) && $spp->tagihan < $siswa->tarif_spp;
                                                    $potongan = $hasDispensasi ? ($siswa->tarif_spp - $spp->tagihan) : 0;
                                                    $namaDispensasi = $siswa->dispensasi->nama ?? 'Dispensasi';
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
                                                            @if($hasDispensasi)
                                                                <p class="text-xs text-purple-700 font-medium mt-0.5 flex items-center gap-1">
                                                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-600 inline-block"></span>
                                                                    Potongan Dispensasi ({{ $namaDispensasi }}): -{{ format_rupiah($potongan) }} (Normal: {{ format_rupiah($siswa->tarif_spp) }})
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <p
                                                            class="font-semibold text-sm {{ $isTerlewat ? 'text-red-700' : '' }}">
                                                            {{ format_rupiah($nominal) }}</p>
                                                        @if ($lunas)
                                                            <span class="badge-green text-xs">Lunas</span>
                                                        @elseif($hasDispensasi)
                                                            <span class="inline-block text-xs font-semibold text-purple-700 bg-purple-50 border border-purple-200 px-2 py-0.5 rounded">{{ $namaDispensasi }}</span>
                                                        @elseif($isTerlewat)
                                                            <span class="badge-red text-xs">Terlewat</span>
                                                        @else
                                                            <span class="badge-yellow text-xs">Belum Bayar</span>
                                                        @endif
                                                    </div>
                                                </label>
                                            @endforeach
                                        @else
                                            <div
                                                class="p-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                                <p class="text-sm font-medium">Tidak ada tagihan SPP untuk siswa ini.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Tabungan Wajib Section --}}
                                <div id="section-tabungan_wajib" class="space-y-4 hidden">
                                    <div class="space-y-2">
                                        <label class="flex items-center justify-between p-4 rounded-xl border cursor-pointer transition-all border-gray-200 hover:border-emerald-400 hover:bg-emerald-50/50 bg-white">
                                            <div class="flex items-center gap-4">
                                                <input type="checkbox" name="items[tabungan_wajib]" value="1" id="dash-checkbox-tabungan-wajib"
                                                    data-tagihan-nominal="{{ $tarifTabunganWajib ?? 0 }}"
                                                    class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500 shrink-0">
                                                <div class="space-y-0.5">
                                                    <p class="text-sm font-bold text-gray-900">Tabungan Wajib</p>
                                                    <p class="text-xs text-gray-500">Tarif per kelas: <span id="dash-text-tarif-tabungan-wajib" class="font-semibold text-emerald-700">{{ format_rupiah($tarifTabunganWajib ?? 0) }}</span></p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="relative flex items-center">
                                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400 select-none">Rp</span>
                                                    <input type="number" name="nominal_tabungan_wajib" id="dash-input-nominal-tabungan-wajib"
                                                        value="{{ $tarifTabunganWajib ?? 0 }}"
                                                        class="form-input text-xs pl-9 pr-3 font-bold text-gray-900 w-36 text-right rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                                        min="0" step="1000" placeholder="Nominal">
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- Iuran & Lainnya Section --}}
                                <div id="section-iuran" class="space-y-4 hidden">
                                    <div id="dash-list-items-iuran" class="space-y-2">
                                        @if (isset($tagihanIuran) && $tagihanIuran->isNotEmpty())
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
                                                        <div class="flex-1">
                                                            <span
                                                                class="text-sm font-medium text-gray-900 block">{{ $iuran->jenisPenerimaan->nama }}</span>
                                                            <span class="text-xs text-gray-500 block">
                                                                {{ $iuran->status === 'cicilan' ? 'Cicilan — Sisa bayar' : ($lunas ? 'Sudah lunas' : 'Belum dibayar') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="font-semibold text-sm">{{ format_rupiah($nominal) }}</p>
                                                        @if ($lunas)
                                                            <span class="badge-green text-xs">Lunas</span>
                                                        @endif
                                                    </div>
                                                </label>
                                            @endforeach
                                        @endif
                                    </div>

                                    {{-- Tunggakan --}}
                                    <div id="dash-wrapper-tunggakan" class="{{ ($sisaTunggakan ?? 0) > 0 ? '' : 'hidden' }}">
                                        <div
                                            class="p-3 rounded-lg border border-amber-200 bg-amber-50 flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <input type="checkbox" name="items[tunggakan]" value="1"
                                                    data-tagihan-nominal="{{ $sisaTunggakan ?? 0 }}"
                                                    class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">Cicil/Lunasi Tunggakan</p>
                                                    <p class="text-xs text-amber-700">Sisa:
                                                        <span id="dash-text-sisa-tunggakan-iuran">{{ format_rupiah($sisaTunggakan ?? 0) }}</span></p>
                                                </div>
                                            </div>
                                            <div>
                                                <input type="number" name="nominal_tunggakan"
                                                    value="{{ $sisaTunggakan ?? 0 }}"
                                                    class="form-input w-36 text-right text-sm" min="0"
                                                    max="{{ $sisaTunggakan ?? 0 }}" step="1000"
                                                    placeholder="Nominal cicil">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Jika Keduanya Kosong --}}
                                    @if (!isset($tagihanIuran) || ($tagihanIuran->isEmpty() && ($sisaTunggakan ?? 0) == 0))
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

                                {{-- Custom / Penerimaan Tambahan --}}
                                <div id="section-custom-penerimaan" class="bg-gradient-to-r from-emerald-50/80 to-teal-50/80 rounded-xl p-4 border border-emerald-200 space-y-3">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-emerald-200/60">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-sm shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider truncate">Penerimaan Tambahan / Custom</h4>
                                                <p class="text-[11px] text-gray-500 truncate">Item penerimaan khusus (misal: Seragam, Buku, Infaq Sukarela, dll)</p>
                                            </div>
                                        </div>
                                        <button type="button" onclick="addCustomPenerimaanRowDashboard()" class="btn-secondary btn-sm text-xs flex items-center gap-1.5 border-emerald-300 bg-white hover:bg-emerald-50 text-emerald-800 font-semibold shadow-sm shrink-0 self-start sm:self-auto">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            <span>+ Tambah Item</span>
                                        </button>
                                    </div>

                                    <div id="custom-penerimaan-list-dashboard" class="space-y-2">
                                        {{-- Custom rows dynamically inserted here --}}
                                    </div>
                                </div>

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
                                <button type="button" onclick="showModalKonfirmasiDashboard()" id="btn-submit-penerimaan-dashboard" class="btn-primary">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Proses Pembayaran</span>
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

    {{-- Modal Konfirmasi Pembayaran --}}
    <div id="modal-konfirmasi-penerimaan" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-md hidden opacity-0 transition-all duration-200">
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 max-w-md w-full mx-4 overflow-hidden transform scale-95 transition-all duration-200" id="modal-konfirmasi-card">
            
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-emerald-600 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-base leading-tight">Konfirmasi Pembayaran</h3>
                        <p class="text-xs text-emerald-100 font-normal">Periksa rincian sebelum menyimpan</p>
                    </div>
                </div>
                <button type="button" onclick="closeModalKonfirmasi()" class="text-emerald-100 hover:text-white focus:outline-none p-1 rounded-lg hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            {{-- Body --}}
            <div class="p-6 space-y-4 text-sm bg-white">
                
                {{-- Student Box --}}
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-2">
                    <div class="flex items-center justify-between text-xs text-gray-500 pb-2 border-b border-gray-200/60">
                        <span class="font-semibold uppercase tracking-wider text-gray-400">Informasi Siswa</span>
                        <span id="modal-no-induk-siswa" class="font-mono bg-white px-2 py-0.5 rounded border border-gray-200 font-semibold text-gray-700"></span>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <div>
                            <h4 id="modal-nama-siswa" class="font-bold text-gray-900 text-base leading-tight"></h4>
                            <p id="modal-kelas-siswa" class="text-xs text-emerald-600 font-semibold mt-0.5"></p>
                        </div>
                        <div class="shrink-0 bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-1 rounded-full">
                            Aktif
                        </div>
                    </div>
                </div>

                {{-- Selected Items --}}
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Item Pembayaran Terpilih</p>
                    <div id="modal-list-items" class="space-y-2 max-h-52 overflow-y-auto pr-1">
                    </div>
                </div>

                {{-- Catatan --}}
                <div id="modal-catatan-wrapper" class="hidden text-xs bg-amber-50/80 border border-amber-200 p-3 rounded-xl">
                    <span class="font-semibold text-amber-800 block mb-0.5">Catatan Tambahan:</span>
                    <span id="modal-catatan-text" class="text-amber-900 font-normal"></span>
                </div>

                {{-- Total Bayar Footer Box --}}
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-emerald-800 font-medium">Total Pembayaran</p>
                        <p class="text-xs text-gray-500">Tunai / Transfer</p>
                    </div>
                    <div class="text-right">
                        <span id="modal-total-bayar" class="text-2xl font-black text-emerald-700 tracking-tight"></span>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModalKonfirmasi()" class="btn-secondary">
                    Batal
                </button>
                <button type="button" id="btn-submit-final-modal" onclick="submitFormPenerimaanFinal()" class="btn-primary shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Ya, Proses Pembayaran</span>
                </button>
            </div>
        </div>
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

    {{-- Script Tab Switcher & AJAX --}}
    <script>
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        }

        let customRowIndexDashboard = 0;

        function addCustomPenerimaanRowDashboard(nama = '', nominal = '') {
            const listContainer = document.getElementById('custom-penerimaan-list-dashboard');
            if (!listContainer) return;

            const rowId = `custom-row-dash-${customRowIndexDashboard}`;
            const div = document.createElement('div');
            div.id = rowId;
            div.className = "custom-item-row flex flex-wrap sm:flex-nowrap items-center gap-3 bg-white p-3 rounded-xl border border-emerald-200 shadow-sm transition-all";

            div.innerHTML = `
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="custom_items[${customRowIndexDashboard}][nama]" value="${nama}"
                        class="form-input text-xs input-custom-nama font-medium"
                        placeholder="Nama Penerimaan (misal: Seragam, Buku, Infaq...)" required>
                </div>
                <div class="w-40 shrink-0">
                    <div class="relative flex items-center">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400 pointer-events-none select-none z-10">Rp</span>
                        <input type="number" name="custom_items[${customRowIndexDashboard}][nominal]" value="${nominal}"
                            class="form-input text-xs pl-9 pr-3 font-bold text-gray-900 input-custom-nominal w-full"
                            min="1" step="500" placeholder="Nominal" required>
                    </div>
                </div>
                <button type="button" onclick="removeCustomPenerimaanRowDashboard('${rowId}')"
                    class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors shrink-0" title="Hapus Item">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            `;

            listContainer.appendChild(div);
            customRowIndexDashboard++;

            div.querySelectorAll('input').forEach(input => {
                input.addEventListener('input', updateTotalBayarDashboard);
                input.addEventListener('change', updateTotalBayarDashboard);
            });

            updateTotalBayarDashboard();
        }

        function removeCustomPenerimaanRowDashboard(rowId) {
            const row = document.getElementById(rowId);
            if (row) {
                row.remove();
                updateTotalBayarDashboard();
            }
        }

        function updateTotalBayarDashboard() {
            let total = 0;
            const checkedCheckboxes = document.querySelectorAll('#form-penerimaan input[type="checkbox"]:checked');

            // Highlight selected labels
            document.querySelectorAll('#form-penerimaan label').forEach(label => {
                const cb = label.querySelector('input[type="checkbox"]');
                if (cb && !cb.disabled) {
                    if (cb.checked) {
                        label.classList.add('border-emerald-500', 'bg-emerald-50/70', 'ring-1', 'ring-emerald-500');
                        label.classList.remove('border-gray-200');
                    } else {
                        label.classList.remove('border-emerald-500', 'bg-emerald-50/70', 'ring-1', 'ring-emerald-500');
                        label.classList.add('border-gray-200');
                    }
                }
            });

            checkedCheckboxes.forEach(cb => {
                if (cb.name === 'items[tunggakan]') {
                    const nominalInput = document.querySelector('input[name="nominal_tunggakan"]');
                    total += parseInt(nominalInput?.value || 0, 10);
                } else if (cb.name === 'items[tabungan_wajib]') {
                    const nominalInput = document.querySelector('input[name="nominal_tabungan_wajib"]');
                    total += parseInt(nominalInput?.value || 0, 10);
                } else {
                    total += parseInt(cb.dataset.tagihanNominal || 0, 10);
                }
            });

            // Add Custom items (only if visible on SPP tab)
            const customSec = document.getElementById('section-custom-penerimaan');
            if (customSec && !customSec.classList.contains('hidden')) {
                document.querySelectorAll('.input-custom-nominal').forEach(input => {
                    const val = parseInt(input.value || 0, 10);
                    if (val > 0) {
                        total += val;
                    }
                });
            }

            const inputTotal = document.getElementById('total-bayar-input');
            const displayTotal = document.getElementById('total-bayar-display');
            if (inputTotal) inputTotal.value = total;
            if (displayTotal) displayTotal.innerText = formatRupiah(total);
        }

        function switchTab(tabName) {
            const sppSec = document.getElementById('section-spp');
            const twSec = document.getElementById('section-tabungan_wajib');
            const iuranSec = document.getElementById('section-iuran');
            const tunggakanSec = document.getElementById('section-tunggakan');
            const customSec = document.getElementById('section-custom-penerimaan');

            if (sppSec) sppSec.classList.add('hidden');
            if (twSec) twSec.classList.add('hidden');
            if (iuranSec) iuranSec.classList.add('hidden');
            if (tunggakanSec) tunggakanSec.classList.add('hidden');

            const activeSec = document.getElementById('section-' + tabName);
            if (activeSec) activeSec.classList.remove('hidden');

            if (customSec) {
                if (tabName === 'spp') {
                    customSec.classList.remove('hidden');
                } else {
                    customSec.classList.add('hidden');
                }
            }

            const checkoutSec = document.getElementById('section-checkout');
            const actionsSec = document.getElementById('section-actions');
            if (checkoutSec) checkoutSec.classList.remove('hidden');
            if (actionsSec) actionsSec.classList.remove('hidden');

            const sppBtn = document.getElementById('tab-btn-spp');
            const twBtn = document.getElementById('tab-btn-tabungan_wajib');
            const iuranBtn = document.getElementById('tab-btn-iuran');

            [sppBtn, twBtn, iuranBtn].forEach(btn => {
                if (btn) {
                    btn.className =
                        "tab-btn p-3 rounded-xl border-2 text-center transition-all focus:outline-none flex-1 border-gray-200 bg-white text-gray-700 font-medium hover:border-gray-300 hover:bg-gray-50";
                    const desc = btn.querySelector('.tab-desc');
                    if (desc) desc.className = "tab-desc block text-xs font-normal text-gray-500 mt-0.5";
                }
            });

            const activeBtn = document.getElementById('tab-btn-' + tabName);
            if (activeBtn) {
                activeBtn.className =
                    "tab-btn p-3 rounded-xl border-2 text-center transition-all focus:outline-none border-emerald-600 bg-emerald-50 text-emerald-800 font-semibold shadow-sm flex-1";
                const desc = activeBtn.querySelector('.tab-desc');
                if (desc) desc.className = "tab-desc block text-xs font-normal text-emerald-600 mt-0.5";
            }

            updateTotalBayarDashboard();
        }

        function renderDashboardDetailSiswa(data) {
            const s = data.siswa;
            document.getElementById('dash-detail-nama').innerText = s.nama;
            document.getElementById('dash-detail-no-induk').innerText = s.no_induk;
            document.getElementById('dash-detail-kelas').innerText = s.kelas;
            document.getElementById('dash-detail-tahun-ajaran').innerText = s.tahun_ajaran;
            document.getElementById('dash-detail-tarif-spp').innerText = formatRupiah(s.tarif_spp);
            document.getElementById('dash-input-siswa-tahun-ajaran-id').value = s.id;

            // Tabungan Wajib
            if (s.tarif_tabungan_wajib !== undefined) {
                const textTw = document.getElementById('dash-text-tarif-tabungan-wajib');
                const inputTw = document.getElementById('dash-input-nominal-tabungan-wajib');
                const cbTw = document.getElementById('dash-checkbox-tabungan-wajib');
                if (textTw) textTw.innerText = formatRupiah(s.tarif_tabungan_wajib);
                if (inputTw) inputTw.value = s.tarif_tabungan_wajib;
                if (cbTw) cbTw.dataset.tagihanNominal = s.tarif_tabungan_wajib;
            }

            const tunggakanWrapper = document.getElementById('dash-detail-sisa-tunggakan-wrapper');
            const alertTunggakan = document.getElementById('dash-alert-tunggakan');
            const wrapperTunggakan = document.getElementById('dash-wrapper-tunggakan');

            if (s.tunggakan_awal > 0 && data.sisaTunggakan > 0) {
                if (tunggakanWrapper) {
                    tunggakanWrapper.classList.remove('hidden');
                    document.getElementById('dash-detail-sisa-tunggakan').innerText = formatRupiah(data.sisaTunggakan);
                }
                if (alertTunggakan) {
                    alertTunggakan.classList.remove('hidden');
                    document.getElementById('dash-text-sisa-tunggakan-alert').innerText = formatRupiah(data.sisaTunggakan);
                }
                if (wrapperTunggakan) {
                    wrapperTunggakan.classList.remove('hidden');
                    document.getElementById('dash-text-sisa-tunggakan-iuran').innerText = formatRupiah(data.sisaTunggakan);
                }
            } else {
                if (tunggakanWrapper) tunggakanWrapper.classList.add('hidden');
                if (alertTunggakan) alertTunggakan.classList.add('hidden');
                if (wrapperTunggakan) wrapperTunggakan.classList.add('hidden');
            }

            // Render SPP items
            const sppListEl = document.getElementById('dash-list-items-spp');
            if (data.tagihanSpp && data.tagihanSpp.length > 0) {
                let html = '';
                const currentYear = new Date().getFullYear();
                const currentMonth = new Date().getMonth() + 1;

                data.tagihanSpp.forEach(spp => {
                    const lunas = spp.lunas;
                    const nominal = spp.nominal;
                    const hasDispensasi = spp.hasDispensasi;
                    const potongan = spp.potongan;
                    const namaDispensasi = spp.namaDispensasi || 'Dispensasi';
                    const isTerlewat = !lunas && (spp.tahun < currentYear || (spp.tahun === currentYear && spp.bulan < currentMonth));

                    html += `
                    <label class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-all ${lunas ? 'border-gray-200 bg-gray-50 opacity-60 cursor-not-allowed' : (isTerlewat ? 'border-red-200 bg-red-50/30 hover:border-red-400 hover:bg-red-50' : 'border-gray-200 hover:border-emerald-400 hover:bg-emerald-50')}">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="items[spp][]" value="${spp.id}"
                                data-tagihan-nominal="${nominal}" ${lunas ? 'disabled' : ''}
                                class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            <div>
                                <p class="text-sm font-medium text-gray-900">SPP ${spp.nama}</p>
                                <p class="text-xs ${isTerlewat ? 'text-red-600 font-medium' : 'text-gray-500'}">
                                    ${spp.status === 'cicilan' ? 'Cicilan — Sisa bayar' : (lunas ? 'Sudah lunas' : (isTerlewat ? 'Terlewat / Belum dibayar' : 'Belum dibayar'))}
                                </p>
                                ${hasDispensasi ? `
                                    <p class="text-xs text-purple-700 font-medium mt-0.5 flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-600 inline-block"></span>
                                        Potongan Dispensasi (${namaDispensasi}): -${formatRupiah(potongan)} (Normal: ${formatRupiah(s.tarif_spp)})
                                    </p>
                                ` : ''}
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-sm ${isTerlewat ? 'text-red-700' : ''}">${formatRupiah(nominal)}</p>
                            ${lunas ? '<span class="badge-green text-xs">Lunas</span>' : (hasDispensasi ? `<span class="inline-block text-xs font-semibold text-purple-700 bg-purple-50 border border-purple-200 px-2 py-0.5 rounded">${namaDispensasi}</span>` : (isTerlewat ? '<span class="badge-red text-xs">Terlewat</span>' : '<span class="badge-yellow text-xs">Belum Bayar</span>'))}
                        </div>
                    </label>`;
                });
                sppListEl.innerHTML = html;
            } else {
                sppListEl.innerHTML = '<div class="p-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200"><p class="text-sm font-medium">Tidak ada tagihan SPP untuk siswa ini.</p></div>';
            }

            // Render Iuran items
            const iuranListEl = document.getElementById('dash-list-items-iuran');
            if (data.tagihanIuran && data.tagihanIuran.length > 0) {
                let html = '';
                data.tagihanIuran.forEach(iuran => {
                    const lunas = iuran.lunas;
                    const nominal = iuran.nominal;
                    html += `
                    <label class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-all ${lunas ? 'border-gray-200 bg-gray-50 opacity-60 cursor-not-allowed' : 'border-gray-200 hover:border-emerald-400 hover:bg-emerald-50'}">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="items[iuran][]" value="${iuran.id}"
                                data-tagihan-nominal="${nominal}" ${lunas ? 'disabled' : ''}
                                class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-900 block">${iuran.nama}</span>
                                <span class="text-xs text-gray-500 block">
                                    ${iuran.status === 'cicilan' ? 'Cicilan — Sisa bayar' : (lunas ? 'Sudah lunas' : 'Belum dibayar')}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-sm">${formatRupiah(nominal)}</p>
                            ${lunas ? '<span class="badge-green text-xs">Lunas</span>' : ''}
                        </div>
                    </label>`;
                });
                iuranListEl.innerHTML = html;
            } else {
                iuranListEl.innerHTML = '';
            }

            const container = document.getElementById('container-detail-transaksi-dashboard');
            container.classList.remove('hidden');

            switchTab('spp');
            updateTotalBayarDashboard();
        }

        function updateDashboardSelectedStudentRows(selectedNoInduk) {
            document.querySelectorAll('.siswa-row-dashboard').forEach(tr => {
                const isSelected = tr.dataset.noInduk === selectedNoInduk;

                if (isSelected) {
                    tr.classList.add('bg-emerald-50/50');
                } else {
                    tr.classList.remove('bg-emerald-50/50');
                }

                const badge = tr.querySelector('.badge-terpilih-dashboard');
                if (badge) {
                    if (isSelected) {
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }

                const btn = tr.querySelector('.btn-catat-dashboard');
                if (btn) {
                    if (isSelected) {
                        btn.innerText = 'Terpilih';
                        btn.className = "btn-catat-dashboard btn-sm w-full block text-center transition-all bg-emerald-50 text-emerald-700 border-2 border-emerald-500 font-semibold";
                    } else {
                        btn.innerText = 'Catat';
                        btn.className = "btn-catat-dashboard btn-sm w-full block text-center transition-all bg-emerald-600 hover:bg-emerald-700 text-white font-medium shadow-sm";
                    }
                }
            });
        }

        function showModalKonfirmasiDashboard() {
            const selectedItems = document.querySelectorAll('#form-penerimaan input[type="checkbox"]:checked');
            const customSec = document.getElementById('section-custom-penerimaan');
            let hasValidCustom = false;
            if (customSec && !customSec.classList.contains('hidden')) {
                document.querySelectorAll('.custom-item-row').forEach(row => {
                    const customNama = row.querySelector('.input-custom-nama')?.value?.trim();
                    const customNominal = parseInt(row.querySelector('.input-custom-nominal')?.value || 0, 10);
                    if (customNama && customNominal > 0) {
                        hasValidCustom = true;
                    }
                });
            }

            if (selectedItems.length === 0 && !hasValidCustom) {
                alert('Silakan centang tagihan atau isi minimal 1 penerimaan tambahan.');
                return;
            }
            const totalBayar = parseInt(document.getElementById('total-bayar-input')?.value || 0, 10);

            // Student info
            const nama = document.getElementById('dash-detail-nama')?.innerText || '-';
            const noInduk = document.getElementById('dash-detail-no-induk')?.innerText || '';
            const kelas = document.getElementById('dash-detail-kelas')?.innerText || '';

            document.getElementById('modal-nama-siswa').innerText = nama;
            document.getElementById('modal-no-induk-siswa').innerText = noInduk;
            document.getElementById('modal-kelas-siswa').innerText = kelas ? 'Kelas ' + kelas : '';
            document.getElementById('modal-total-bayar').innerText = formatRupiah(totalBayar);

            // Collect selected items
            let itemsHtml = '';
            document.querySelectorAll('#form-penerimaan input[type="checkbox"]:checked').forEach(cb => {
                let itemNama = '';
                let itemNominal = 0;

                const parentLabel = cb.closest('label');
                if (cb.name === 'items[tunggakan]') {
                    itemNama = 'Cicilan / Lunasi Tunggakan';
                    const nominalInput = document.querySelector('input[name="nominal_tunggakan"]');
                    itemNominal = parseInt(nominalInput?.value || 0, 10);
                } else if (cb.name === 'items[tabungan_wajib]') {
                    itemNama = 'Tabungan Wajib';
                    const nominalInput = document.querySelector('input[name="nominal_tabungan_wajib"]');
                    itemNominal = parseInt(nominalInput?.value || 0, 10);
                } else if (parentLabel) {
                    const namaEl = parentLabel.querySelector('p.font-medium, span.font-medium');
                    itemNama = namaEl ? namaEl.innerText.trim() : 'Item Pembayaran';
                    itemNominal = parseInt(cb.dataset.tagihanNominal || 0, 10);
                }

                itemsHtml += `
                    <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-200 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="font-semibold text-gray-800">${itemNama}</span>
                        </div>
                        <span class="font-bold text-gray-900 text-sm">${formatRupiah(itemNominal)}</span>
                    </div>
                `;
            });

            // Collect Custom items (only if visible)
            if (customSec && !customSec.classList.contains('hidden')) {
                document.querySelectorAll('.custom-item-row').forEach(row => {
                    const customNama = row.querySelector('.input-custom-nama')?.value?.trim();
                    const customNominal = parseInt(row.querySelector('.input-custom-nominal')?.value || 0, 10);
                    if (customNama && customNominal > 0) {
                        itemsHtml += `
                            <div class="flex items-center justify-between p-3 rounded-xl bg-teal-50 border border-teal-200 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-teal-500 shrink-0"></span>
                                    <div>
                                        <span class="font-semibold text-gray-800">${customNama}</span>
                                        <span class="text-[10px] text-teal-600 block font-normal">Penerimaan Tambahan</span>
                                    </div>
                                </div>
                                <span class="font-bold text-gray-900 text-sm">${formatRupiah(customNominal)}</span>
                            </div>
                        `;
                    }
                });
            }

            document.getElementById('modal-list-items').innerHTML = itemsHtml;

            // Catatan
            const catatan = document.querySelector('#form-penerimaan textarea[name="catatan"]')?.value?.trim();
            const catatanWrapper = document.getElementById('modal-catatan-wrapper');
            if (catatan) {
                document.getElementById('modal-catatan-text').innerText = catatan;
                catatanWrapper.classList.remove('hidden');
            } else {
                catatanWrapper.classList.add('hidden');
            }

            // Show modal animation
            const modal = document.getElementById('modal-konfirmasi-penerimaan');
            const card = document.getElementById('modal-konfirmasi-card');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                card.classList.remove('scale-95');
                card.classList.add('scale-100');
            }, 10);
        }

        function closeModalKonfirmasi() {
            const modal = document.getElementById('modal-konfirmasi-penerimaan');
            const card = document.getElementById('modal-konfirmasi-card');
            if (!modal) return;
            modal.classList.add('opacity-0');
            card.classList.remove('scale-100');
            card.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }

        function submitFormPenerimaanFinal() {
            const btn = document.getElementById('btn-submit-final-modal');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');
                const span = btn.querySelector('span');
                if (span) span.innerText = 'Memproses...';
            }
            document.getElementById('form-penerimaan').submit();
        }

        // Attach AJAX to dashboard "Catat" buttons
        window.bindCatatDashboardButtons = function() {
            document.querySelectorAll('.btn-catat-dashboard').forEach(btn => {
                if (btn.dataset.bound) return;
                btn.dataset.bound = "true";
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const noInduk = this.dataset.noInduk;
                    const originalText = this.innerText;
                    this.innerText = 'Memuat...';
                    this.classList.add('opacity-75');

                    fetch(`{{ route('dashboard') }}?no_induk=${encodeURIComponent(noInduk)}&ajax=1`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            this.innerText = originalText;
                            this.classList.remove('opacity-75');
                            return;
                        }

                        updateDashboardSelectedStudentRows(noInduk);

                        const newUrl = `{{ route('dashboard') }}?no_induk=${encodeURIComponent(noInduk)}`;
                        window.history.pushState({ path: newUrl }, '', newUrl);

                        renderDashboardDetailSiswa(data);
                    })
                    .catch(err => {
                        this.innerText = originalText;
                        this.classList.remove('opacity-75');
                        console.error(err);
                        alert('Gagal memuat data siswa via AJAX.');
                    });
                });
            });
        };

        document.addEventListener('DOMContentLoaded', function() {
            const formPenerimaan = document.getElementById('form-penerimaan');
            if (formPenerimaan) {
                formPenerimaan.addEventListener('change', updateTotalBayarDashboard);
                formPenerimaan.addEventListener('input', updateTotalBayarDashboard);

                formPenerimaan.addEventListener('submit', function(e) {
                    const selectedItems = document.querySelectorAll('#form-penerimaan input[type="checkbox"]:checked');
                    const customSec = document.getElementById('section-custom-penerimaan');
                    let hasValidCustom = false;
                    if (customSec && !customSec.classList.contains('hidden')) {
                        document.querySelectorAll('.custom-item-row').forEach(row => {
                            const customNama = row.querySelector('.input-custom-nama')?.value?.trim();
                            const customNominal = parseInt(row.querySelector('.input-custom-nominal')?.value || 0, 10);
                            if (customNama && customNominal > 0) {
                                hasValidCustom = true;
                            }
                        });
                    }

                    if (selectedItems.length === 0 && !hasValidCustom) {
                        e.preventDefault();
                        alert('Silakan centang tagihan atau isi minimal 1 penerimaan tambahan.');
                        return false;
                    }
                });
            }

            const inputNominalTw = document.getElementById('dash-input-nominal-tabungan-wajib');
            if (inputNominalTw) {
                inputNominalTw.addEventListener('input', updateTotalBayarDashboard);
                inputNominalTw.addEventListener('change', updateTotalBayarDashboard);
            }

            // Direct click handlers for tab buttons on dashboard
            document.addEventListener('click', function(e) {
                const sppBtn = e.target.closest('#tab-btn-spp');
                if (sppBtn) {
                    e.preventDefault();
                    switchTab('spp');
                }
                const twBtn = e.target.closest('#tab-btn-tabungan_wajib');
                if (twBtn) {
                    e.preventDefault();
                    switchTab('tabungan_wajib');
                }
                const iuranBtn = e.target.closest('#tab-btn-iuran');
                if (iuranBtn) {
                    e.preventDefault();
                    switchTab('iuran');
                }
            });

            window.bindCatatDashboardButtons();

            @if(isset($siswa))
                switchTab('spp');
                updateTotalBayarDashboard();
            @endif
        });
    </script>
</x-layouts.app>
