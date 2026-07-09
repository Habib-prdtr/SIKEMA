<x-layouts.app title="Rincian Pembayar">
    <x-slot:pageTitle>Master Data / Jenis Penerimaan / Rincian Pembayar</x-slot:pageTitle>

    <div class="space-y-5">
        <!-- Back Button & Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('master.jenis-penerimaan.index') }}" class="btn-secondary p-2.5 rounded-lg flex items-center justify-center hover:bg-gray-100 transition-colors" title="Kembali">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Rincian Pembayar: {{ $jenisPenerimaan->nama }}</h1>
                    <p class="text-gray-500 text-sm mt-0.5">Tahun Ajaran: <span class="font-semibold text-emerald-600">{{ $jenisPenerimaan->tahunAjaran->nama }}</span></p>
                </div>
            </div>
            <div>
                <a href="{{ route('master.jenis-penerimaan.index') }}" class="btn-secondary inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"/>
                    </svg>
                    <span>Kembali ke Daftar</span>
                </a>
            </div>
        </div>

        <!-- Statistik Penerimaan -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Total Terkumpul -->
            <div class="card p-4 bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md border-0">
                <div class="text-xs font-semibold uppercase tracking-wider opacity-85">Total Terkumpul</div>
                <div class="text-2xl font-extrabold mt-1.5 tracking-tight">{{ format_rupiah($totalTerkumpul) }}</div>
                <div class="text-xs mt-2 opacity-75">Dari tarif iuran: {{ format_rupiah($jenisPenerimaan->tarif) }}</div>
            </div>

            <!-- Lunas -->
            <div class="card p-4 border border-gray-100 bg-white shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Lunas</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1.5">{{ $lunasCount }} <span class="text-sm font-normal text-gray-400">Siswa</span></div>
                </div>
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Belum Lunas -->
            <div class="card p-4 border border-gray-100 bg-white shadow-sm flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Belum Lunas</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1.5">{{ $belumCount }} <span class="text-sm font-normal text-gray-400">Siswa</span></div>
                </div>
                <div class="w-10 h-10 bg-red-50 text-red-500 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Tabel Riwayat Siswa -->
        <div class="card">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">Daftar Siswa Sudah Membayar</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Menampilkan semua siswa dengan kontribusi pembayaran (terbayar > 0) pada iuran ini</p>
                </div>
                <span class="text-xs text-gray-500 bg-gray-50 border border-gray-150 px-3 py-1 rounded-full font-medium">
                    {{ count($tagihanList) }} Pembayar
                </span>
            </div>

            @if($tagihanList->isEmpty())
                <div class="p-12 text-center text-gray-400">
                    <svg class="w-14 h-14 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-sm">Belum ada siswa yang melakukan pembayaran untuk jenis iuran ini.</p>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No. Induk</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th class="text-right">Total Tagihan</th>
                                <th class="text-right">Telah Dibayar</th>
                                <th class="text-center">Status</th>
                                <th class="text-right">Sisa Tagihan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tagihanList as $t)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="font-mono text-xs text-gray-600">{{ $t->siswaTahunAjaran->siswa->no_induk }}</td>
                                    <td class="font-medium text-gray-900">{{ $t->siswaTahunAjaran->siswa->nama }}</td>
                                    <td class="text-gray-700">{{ $t->siswaTahunAjaran->siswa->kelas }}</td>
                                    <td class="text-right font-medium text-gray-700">
                                        {{ format_rupiah($t->tagihan) }}
                                    </td>
                                    <td class="text-right font-bold text-emerald-700">
                                        {{ format_rupiah($t->terbayar) }}
                                    </td>
                                    <td class="text-center">
                                        @if($t->status === 'lunas')
                                            <span class="badge-green">Lunas</span>
                                        @else
                                            <span class="badge-red">Belum Lunas</span>
                                        @endif
                                    </td>
                                    <td class="text-right font-medium text-gray-600">
                                        @if($t->sisa() > 0)
                                            <span class="text-amber-700 font-semibold">{{ format_rupiah($t->sisa()) }}</span>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
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
