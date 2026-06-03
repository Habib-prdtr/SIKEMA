<x-layouts.app title="Dashboard">
    <x-slot:pageTitle>Dashboard</x-slot:pageTitle>

    <div class="space-y-6">

        {{-- Heading --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-500 text-sm mt-0.5">
                Selamat datang, {{ auth()->user()->name }}
                @if($tahunAktif) — Tahun Ajaran <span class="font-semibold text-emerald-600">{{ $tahunAktif->nama }}</span> @endif
            </p>
        </div>

        @if(!$tahunAktif)
            <div class="alert-warning">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-medium">Tahun Ajaran Belum Aktif</p>
                    <p class="text-sm mt-0.5">Silakan aktifkan tahun ajaran terlebih dahulu di menu
                        <a href="{{ route('master.tahun-ajaran.index') }}" class="font-semibold underline">Master Data → Tahun Ajaran</a>.
                    </p>
                </div>
            </div>
        @endif

        {{-- ── Stat Cards ─────────────────────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

            {{-- Saldo Kas --}}
            <div class="card p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Saldo Kas</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ format_rupiah($totalSaldo) }}</p>
                        <p class="text-xs text-gray-400 mt-1">TA {{ $tahunAktif?->nama ?? '-' }}</p>
                    </div>
                    <div class="w-11 h-11 bg-emerald-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Penerimaan Bulan Ini --}}
            <div class="card p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Penerimaan Bulan Ini</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ format_rupiah($totalPenerimaanBulanIni) }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ now()->locale('id')->isoFormat('MMMM YYYY') }}</p>
                    </div>
                    <div class="w-11 h-11 bg-green-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Pengeluaran Bulan Ini --}}
            <div class="card p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pengeluaran Bulan Ini</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ format_rupiah($totalPengeluaranBulanIni) }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ now()->locale('id')->isoFormat('MMMM YYYY') }}</p>
                    </div>
                    <div class="w-11 h-11 bg-red-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17H5m0 0V9m0 8l8-8 4 4 6-6"/>
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
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Banner tunggakan ────────────────────────────────── --}}
        @if($siswaAdaTunggakan > 0)
            <div class="alert-warning">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-medium">Terdapat Tunggakan Aktif</p>
                    <p class="text-sm mt-0.5">
                        <strong>{{ $siswaAdaTunggakan }} siswa</strong> memiliki tunggakan tahun sebelumnya
                        dengan total <strong>{{ format_rupiah($totalTunggakanAwal) }}</strong>.
                    </p>
                </div>
            </div>
        @endif

        @if($sppBelumLunas > 0)
            <div class="alert-info">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>
                    <strong>{{ $sppBelumLunas }} siswa</strong> belum melunasi SPP bulan
                    {{ now()->locale('id')->isoFormat('MMMM YYYY') }}.
                </p>
            </div>
        @endif

        {{-- ── Grafik (SVG Bar Chart sederhana) + Transaksi ───── --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

            {{-- Bar Chart 6 bulan --}}
            <div class="card xl:col-span-2">
                <div class="card-header">
                    <h3 class="font-semibold text-gray-900">Grafik Keuangan 6 Bulan Terakhir</h3>
                </div>
                <div class="p-5">
                    @php
                        $maxVal = max(max($dataPenerimaan->toArray() ?: [1]), max($dataPengeluaran->toArray() ?: [1]));
                        $chartH = 160;
                    @endphp
                    <div class="overflow-x-auto">
                        <svg viewBox="0 0 {{ count($bulanLabels) * 80 }} {{ $chartH + 40 }}"
                             class="w-full min-w-xs" style="min-height:200px">
                            @foreach($bulanLabels as $i => $label)
                                @php
                                    $x       = $i * 80 + 10;
                                    $p       = $dataPenerimaan[$i] ?? 0;
                                    $k       = $dataPengeluaran[$i] ?? 0;
                                    $pHeight = $maxVal > 0 ? ($p / $maxVal * $chartH) : 0;
                                    $kHeight = $maxVal > 0 ? ($k / $maxVal * $chartH) : 0;
                                @endphp
                                {{-- Penerimaan bar --}}
                                <rect x="{{ $x }}" y="{{ $chartH - $pHeight }}"
                                      width="24" height="{{ $pHeight }}"
                                      fill="#059669" rx="3" opacity="0.85">
                                    <title>Penerimaan: {{ format_rupiah($p) }}</title>
                                </rect>
                                {{-- Pengeluaran bar --}}
                                <rect x="{{ $x + 28 }}" y="{{ $chartH - $kHeight }}"
                                      width="24" height="{{ $kHeight }}"
                                      fill="#dc2626" rx="3" opacity="0.75">
                                    <title>Pengeluaran: {{ format_rupiah($k) }}</title>
                                </rect>
                                {{-- Label --}}
                                <text x="{{ $x + 26 }}" y="{{ $chartH + 16 }}"
                                      text-anchor="middle" font-size="11" fill="#6b7280">{{ $label }}</text>
                            @endforeach
                        </svg>
                    </div>
                    <div class="flex items-center gap-5 mt-2 text-xs text-gray-500">
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-emerald-600 inline-block"></span>Penerimaan</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-red-600 inline-block"></span>Pengeluaran</span>
                    </div>
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="space-y-3">
                <a href="{{ route('penerimaan.catat') }}"
                    class="card p-4 flex items-center gap-4 hover:border-emerald-300 hover:shadow-md transition-all group">
                    <div class="w-10 h-10 bg-emerald-100 group-hover:bg-emerald-200 rounded-xl flex items-center justify-center transition-colors shrink-0">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">Catat Penerimaan</p>
                        <p class="text-xs text-gray-500">Proses pembayaran siswa</p>
                    </div>
                </a>
                <a href="{{ route('pengeluaran.catat') }}"
                    class="card p-4 flex items-center gap-4 hover:border-red-200 hover:shadow-md transition-all group">
                    <div class="w-10 h-10 bg-red-100 group-hover:bg-red-200 rounded-xl flex items-center justify-center transition-colors shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">Catat Pengeluaran</p>
                        <p class="text-xs text-gray-500">Rekam kas keluar</p>
                    </div>
                </a>
                <a href="{{ route('laporan.penerimaan') }}"
                    class="card p-4 flex items-center gap-4 hover:border-blue-200 hover:shadow-md transition-all group">
                    <div class="w-10 h-10 bg-blue-100 group-hover:bg-blue-200 rounded-xl flex items-center justify-center transition-colors shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">Lihat Laporan</p>
                        <p class="text-xs text-gray-500">Rekap keuangan</p>
                    </div>
                </a>
                <a href="{{ route('master.siswa.index') }}"
                    class="card p-4 flex items-center gap-4 hover:border-purple-200 hover:shadow-md transition-all group">
                    <div class="w-10 h-10 bg-purple-100 group-hover:bg-purple-200 rounded-xl flex items-center justify-center transition-colors shrink-0">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">Data Siswa</p>
                        <p class="text-xs text-gray-500">Kelola data siswa</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- ── Transaksi Terbaru ───────────────────────────────── --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">Transaksi Terbaru</h3>
                <a href="{{ route('penerimaan.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    Lihat semua →
                </a>
            </div>
            @if($transaksiTerbaru->isEmpty())
                <div class="p-10 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
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
                            @foreach($transaksiTerbaru as $trx)
                                <tr>
                                    <td class="font-mono text-xs">{{ $trx->no_transaksi }}</td>
                                    <td class="font-medium text-gray-900">{{ $trx->siswaTahunAjaran->siswa->nama }}</td>
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
</x-layouts.app>