<x-layouts.app title="Laporan Penerimaan">
    <x-slot:pageTitle>Laporan / Penerimaan</x-slot:pageTitle>

    <div class="space-y-5">
        <div class="flex items-center justify-between no-print">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Laporan Penerimaan</h1>
                <p class="text-gray-500 text-sm mt-0.5">Rekap transaksi penerimaan</p>
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
        <form method="GET" action="{{ route('laporan.penerimaan') }}" class="card p-4 no-print">
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
                @if(request('bulan') || request('tahun'))
                    <div class="flex items-end">
                        <a href="{{ route('laporan.penerimaan') }}" class="btn-secondary">Reset</a>
                    </div>
                @endif
            </div>
        </form>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="card p-5 border-l-4 border-l-emerald-500">
                <p class="text-xs text-gray-500 uppercase font-semibold tracking-wider">Total Penerimaan</p>
                <p class="text-2xl font-bold text-emerald-700 mt-1">Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $transaksi->count() }} transaksi</p>
            </div>
            <div class="card p-5 border-l-4 border-l-blue-500">
                <p class="text-xs text-gray-500 uppercase font-semibold tracking-wider">Total SPP</p>
                <p class="text-2xl font-bold text-blue-700 mt-1">Rp {{ number_format($totalSpp, 0, ',', '.') }}</p>
            </div>
            <div class="card p-5 border-l-4 border-l-purple-500">
                <p class="text-xs text-gray-500 uppercase font-semibold tracking-wider">Total Iuran & Tunggakan</p>
                <p class="text-2xl font-bold text-purple-700 mt-1">Rp {{ number_format($totalIuran + $totalTunggakan, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">Detail Transaksi</h3>
                <p class="text-sm text-gray-500">
                    @if(request('bulan'))
                        {{ \Carbon\Carbon::create(null, request('bulan'))->locale('id')->isoFormat('MMMM') }}
                    @endif
                    {{ request('tahun', date('Y')) }}
                </p>
            </div>
            @if($transaksi->isEmpty())
                <div class="p-10 text-center text-gray-400">
                    <p class="text-sm">Tidak ada data transaksi pada periode ini.</p>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="table text-xs">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. Transaksi</th>
                                <th>Tanggal</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Rincian</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaksi as $i => $trx)
                                <tr>
                                    <td class="text-gray-400">{{ $transaksi->firstItem() + $i }}</td>
                                    <td class="font-mono font-semibold">{{ $trx->no_transaksi }}</td>
                                    <td>{{ $trx->tanggal->format('d/m/Y') }}</td>
                                    <td class="font-medium text-gray-900">{{ $trx->siswaTahunAjaran->siswa->nama }}</td>
                                    <td>{{ $trx->siswaTahunAjaran->siswa->kelas }}</td>
                                    <td class="max-w-xs">
                                        @foreach($trx->details as $d)
                                            <span class="inline-block text-gray-500">
                                                @if($d->jenis === 'spp')SPP {{ \Carbon\Carbon::createFromDate($d->tahun, $d->bulan, 1)->locale('id')->isoFormat('MMM') }}
                                                @elseif($d->jenis === 'iuran'){{ $d->jenisPenerimaan->nama ?? '-' }}
                                                @else Tunggakan
                                                @endif
                                            </span>{{ !$loop->last ? ',' : '' }}
                                        @endforeach
                                    </td>
                                    <td class="text-right font-semibold text-emerald-700">
                                        Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-emerald-50 font-semibold">
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-gray-700">TOTAL</td>
                                <td class="px-4 py-3 text-right text-emerald-700 text-base">
                                    Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($transaksi->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100 no-print">
                        {{ $transaksi->withQueryString()->links() }}
                    </div>
                @endif
            @endif
        </div>

    </div>
</x-layouts.app>
