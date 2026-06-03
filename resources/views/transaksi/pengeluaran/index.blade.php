<x-layouts.app title="Riwayat Pengeluaran">
    <x-slot:pageTitle>Pengeluaran / Riwayat</x-slot:pageTitle>

    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Riwayat Pengeluaran</h1>
                <p class="text-gray-500 text-sm mt-0.5">Daftar semua pengeluaran kas</p>
            </div>
            <a href="{{ route('pengeluaran.catat') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Catat Pengeluaran
            </a>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('pengeluaran.index') }}" class="card p-4">
            <div class="flex flex-wrap gap-3">
                <div class="flex-1 min-w-40">
                    <select name="pos_biaya_id" class="form-select">
                        <option value="">Semua Pos</option>
                        @foreach($posList as $pos)
                            <option value="{{ $pos->id }}" {{ request('pos_biaya_id') == $pos->id ? 'selected' : '' }}>
                                {{ $pos->nama }}
                            </option>
                        @endforeach
                    </select>
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
                @if(request('pos_biaya_id') || request('bulan'))
                    <a href="{{ route('pengeluaran.index') }}" class="btn-secondary">Reset</a>
                @endif
            </div>
        </form>

        <div class="card">
            @if($pengeluaran->isEmpty())
                <div class="p-12 text-center text-gray-400">
                    <svg class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    <p class="text-sm">Belum ada data pengeluaran.</p>
                    <a href="{{ route('pengeluaran.catat') }}" class="btn-primary mt-4">Catat Sekarang</a>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Pos Biaya</th>
                                <th>Keterangan</th>
                                <th>Operator</th>
                                <th class="text-right">Jumlah</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengeluaran as $p)
                                <tr>
                                    <td class="text-gray-500">{{ $p->tanggal->format('d/m/Y') }}</td>
                                    <td class="font-medium text-gray-900">{{ $p->posBiaya->nama }}</td>
                                    <td class="text-gray-500 text-sm max-w-xs truncate">{{ $p->keterangan ?? '-' }}</td>
                                    <td class="text-gray-500 text-sm">{{ $p->user->name }}</td>
                                    <td class="text-right font-semibold text-red-700">
                                        {{ format_rupiah($p->jumlah) }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('pengeluaran.show', $p) }}"
                                            class="btn-secondary btn-sm">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                    <p class="text-sm text-gray-500">{{ $pengeluaran->total() }} data</p>
                    <p class="font-semibold text-red-700">
                        Total: {{ format_rupiah($pengeluaran->sum('jumlah')) }}
                    </p>
                </div>

                @if($pengeluaran->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $pengeluaran->withQueryString()->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>
