<x-layouts.app title="Riwayat Penerimaan">
    <x-slot:pageTitle>Penerimaan / Riwayat</x-slot:pageTitle>

    <div class="space-y-5">
        <div class="flex items-center justify-between no-print">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Riwayat & Laporan Penerimaan</h1>
                <p class="text-gray-500 text-sm mt-0.5">Rekap dan daftar transaksi penerimaan</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('laporan.penerimaan.export', request()->all()) }}" class="btn-secondary">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel
                </a>
                <button id="btn-print" class="btn-secondary">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak / PDF
                </button>
                <a href="{{ route('penerimaan.catat') }}" class="btn-primary">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Catat Penerimaan
                </a>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="card p-5 border-l-4 border-l-emerald-500">
                <p class="text-xs text-gray-500 uppercase font-semibold tracking-wider">Total Penerimaan</p>
                <p class="text-2xl font-bold text-emerald-700 mt-1">{{ format_rupiah($totalPenerimaan) }}</p>
                <p class="text-xs text-gray-400 mt-1">Periode terpilih</p>
            </div>
            <div class="card p-5 border-l-4 border-l-blue-500">
                <p class="text-xs text-gray-500 uppercase font-semibold tracking-wider">Total SPP</p>
                <p class="text-2xl font-bold text-blue-700 mt-1">{{ format_rupiah($totalSpp) }}</p>
                <p class="text-xs text-gray-400 mt-1">Pembayaran SPP</p>
            </div>
            <div class="card p-5 border-l-4 border-l-purple-500">
                <p class="text-xs text-gray-500 uppercase font-semibold tracking-wider">Total Iuran & Tunggakan</p>
                <p class="text-2xl font-bold text-purple-700 mt-1">{{ format_rupiah($totalIuran + $totalTunggakan) }}</p>
                <p class="text-xs text-gray-400 mt-1">Pembayaran Iuran & Tunggakan</p>
            </div>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('penerimaan.index') }}" class="card p-4 no-print">
            <div class="flex flex-wrap gap-3">
                <div class="flex-1 min-w-40">
                    <input type="text" name="cari" value="{{ request('cari') }}"
                        class="form-input" placeholder="No. transaksi / nama siswa...">
                </div>
                <div class="w-36">
                    <select name="bulan" class="form-select">
                        <option value="">Semua Bulan</option>
                        @foreach(range(1,12) as $b)
                            <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $b)->locale('id')->isoFormat('MMMM') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-28">
                    <input type="number" name="tahun" value="{{ request('tahun', date('Y')) }}"
                        class="form-input" placeholder="Tahun" min="2020" max="2099">
                </div>
                <button type="submit" class="btn-primary">Filter</button>
                @if(request('cari') || request('bulan') || request('tahun'))
                    <a href="{{ route('penerimaan.index') }}" class="btn-secondary">Reset</a>
                @endif
            </div>
        </form>

        <div class="card">
            @if($transaksi->isEmpty())
                <div class="p-12 text-center text-gray-400">
                    <svg class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-sm">Belum ada transaksi penerimaan.</p>
                    <a href="{{ route('penerimaan.catat') }}" class="btn-primary mt-4">Catat Sekarang</a>
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
                                <th>Operator</th>
                                <th class="text-right">Total Bayar</th>
                                <th class="text-center no-print">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaksi as $trx)
                                <tr>
                                    <td class="font-mono text-xs font-semibold text-gray-700">{{ $trx->no_transaksi }}</td>
                                    <td class="font-medium text-gray-900">{{ $trx->siswaTahunAjaran->siswa->nama }}</td>
                                    <td>{{ $trx->siswaTahunAjaran->siswa->kelas }}</td>
                                    <td class="text-gray-500">{{ $trx->tanggal->format('d/m/Y') }}</td>
                                    <td class="text-gray-500 text-sm">{{ $trx->user->name }}</td>
                                    <td class="text-right font-semibold text-emerald-700">
                                        {{ format_rupiah($trx->total_bayar) }}
                                    </td>
                                    <td class="text-center no-print">
                                        <a href="{{ route('penerimaan.show', $trx) }}"
                                            class="btn-secondary btn-sm">Detail / Cetak</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Footer total --}}
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex items-center justify-between no-print">
                    <p class="text-sm text-gray-500">{{ $transaksi->total() }} transaksi</p>
                    <p class="font-semibold text-emerald-700">
                        Total Halaman Ini: {{ format_rupiah($transaksi->sum('total_bayar')) }}
                    </p>
                </div>

                @if($transaksi->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100 no-print">
                        {{ $transaksi->withQueryString()->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    <script>
        document.getElementById('btn-print')?.addEventListener('click', function() {
            window.print();
        });
    </script>
</x-layouts.app>
