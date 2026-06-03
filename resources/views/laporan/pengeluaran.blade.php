<x-layouts.app title="Laporan Pengeluaran">
    <x-slot:pageTitle>Laporan / Pengeluaran</x-slot:pageTitle>

    <div class="space-y-5">
        <div class="flex items-center justify-between no-print">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Laporan Pengeluaran</h1>
                <p class="text-gray-500 text-sm mt-0.5">Rekap pengeluaran per pos biaya</p>
            </div>
            <button id="btn-print" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak / PDF
            </button>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('laporan.pengeluaran') }}" class="card p-4 no-print">
            <div class="flex flex-wrap gap-3">
                <div class="w-36">
                    <label class="form-label text-xs">Bulan</label>
                    <select name="bulan" class="form-select">
                        <option value="">Semua</option>
                        @foreach(range(1,12) as $b)
                            <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $b)->locale('id')->isoFormat('MMMM') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-28">
                    <label class="form-label text-xs">Tahun</label>
                    <input type="number" name="tahun" value="{{ request('tahun', date('Y')) }}"
                        class="form-input" min="2020" max="2099">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn-primary">Filter</button>
                </div>
            </div>
        </form>

        {{-- Summary total --}}
        <div class="card p-5 border-l-4 border-l-red-500">
            <p class="text-xs text-gray-500 uppercase font-semibold tracking-wider">Total Pengeluaran</p>
            <p class="text-3xl font-bold text-red-700 mt-1">{{ format_rupiah($totalPengeluaran) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ count($pengeluaran) }} transaksi</p>
        </div>

        {{-- Rekap per Pos --}}
        @if($rekapPerPos->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">Rekap per Pos Biaya</h3>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Pos Biaya</th>
                            <th class="text-right">Anggaran</th>
                            <th class="text-right">Terpakai</th>
                            <th class="text-right">Sisa</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapPerPos as $rekap)
                            @php
                                $sisa  = ($rekap->anggaran ?? 0) - $rekap->total;
                                $pct   = $rekap->anggaran > 0 ? min(100, $rekap->total / $rekap->anggaran * 100) : 0;
                                $color = $pct >= 100 ? 'bg-red-500' : ($pct >= 75 ? 'bg-amber-500' : 'bg-emerald-500');
                            @endphp
                            <tr>
                                <td class="font-medium text-gray-900">{{ $rekap->nama }}</td>
                                <td class="text-right text-gray-500">
                                    {{ $rekap->anggaran > 0 ? format_rupiah($rekap->anggaran) : '-' }}
                                </td>
                                <td class="text-right font-semibold text-red-700">
                                    {{ format_rupiah($rekap->total) }}
                                </td>
                                <td class="text-right {{ $sisa < 0 ? 'text-red-600 font-bold' : 'text-gray-700' }}">
                                    {{ $rekap->anggaran > 0 ? format_rupiah($sisa) : '-' }}
                                </td>
                                <td class="w-36">
                                    @if($rekap->anggaran > 0)
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                <div class="{{ $color }} h-2 rounded-full" style="width:{{ $pct }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500 w-10 text-right">{{ number_format($pct, 0) }}%</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Tabel Detail --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">Detail Pengeluaran</h3>
            </div>
            @if($pengeluaran->isEmpty())
                <div class="p-10 text-center text-gray-400">
                    <p class="text-sm">Tidak ada data pengeluaran pada periode ini.</p>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="table text-xs">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Pos Biaya</th>
                                <th>Keterangan</th>
                                <th>Operator</th>
                                <th class="text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengeluaran as $i => $p)
                                <tr>
                                    <td class="text-gray-400">{{ $loop->iteration }}</td>
                                    <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                                    <td class="font-medium text-gray-900">{{ $p->posBiaya->nama }}</td>
                                    <td class="text-gray-500 max-w-xs truncate">{{ $p->keterangan }}</td>
                                    <td class="text-gray-500">{{ $p->user->name }}</td>
                                    <td class="text-right font-semibold text-red-700">
                                        {{ format_rupiah($p->jumlah) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-red-50 font-semibold">
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-gray-700">TOTAL</td>
                                <td class="px-4 py-3 text-right text-red-700 text-base">
                                    {{ format_rupiah($totalPengeluaran) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>

    </div>
</x-layouts.app>
