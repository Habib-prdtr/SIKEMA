<x-layouts.app title="Riwayat Penerimaan">
    <x-slot:pageTitle>Penerimaan / Riwayat</x-slot:pageTitle>

    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Riwayat Penerimaan</h1>
                <p class="text-gray-500 text-sm mt-0.5">Daftar semua transaksi penerimaan</p>
            </div>
            <a href="{{ route('penerimaan.catat') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Catat Penerimaan
            </a>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('penerimaan.index') }}" class="card p-4">
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
                                <th class="text-center">Aksi</th>
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
                                    <td class="text-center">
                                        <a href="{{ route('penerimaan.show', $trx) }}"
                                            class="btn-secondary btn-sm">Detail / Cetak</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Footer total --}}
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                    <p class="text-sm text-gray-500">{{ $transaksi->total() }} transaksi</p>
                    <p class="font-semibold text-emerald-700">
                        Total: {{ format_rupiah($transaksi->sum('total_bayar')) }}
                    </p>
                </div>

                @if($transaksi->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $transaksi->withQueryString()->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>
