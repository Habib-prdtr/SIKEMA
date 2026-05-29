<x-layouts.app title="Detail Transaksi">
    <x-slot:pageTitle>Penerimaan / Detail Transaksi</x-slot:pageTitle>

    {{-- Tombol aksi (no-print) --}}
    <div class="flex items-center gap-3 mb-5 no-print">
        <a href="{{ route('penerimaan.index') }}" class="btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
        <button id="btn-print" class="btn-primary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak Kwitansi
        </button>
    </div>

    {{-- Kwitansi --}}
    <div class="card max-w-2xl mx-auto" id="kwitansi">

        {{-- Kop --}}
        <div class="px-8 pt-8 pb-5 border-b-2 border-emerald-600 text-center">
            <h2 class="text-xl font-bold text-gray-900 uppercase">{{ $sekolah->nama_sekolah ?? 'MADRASAH' }}</h2>
            @if($sekolah->nama_yayasan ?? false)
                <p class="text-xs text-gray-500">{{ $sekolah->nama_yayasan }}</p>
            @endif
            <div class="mt-3 inline-block bg-emerald-600 text-white px-6 py-1 rounded-full text-sm font-semibold tracking-wide">
                BUKTI PEMBAYARAN / KWITANSI
            </div>
        </div>

        <div class="p-8">
            {{-- No Transaksi & Tanggal --}}
            <div class="flex justify-between items-start mb-6">
                <div>
                    <p class="text-xs text-gray-500">No. Transaksi</p>
                    <p class="font-mono text-lg font-bold text-gray-900">{{ $transaksi->no_transaksi }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500">Tanggal</p>
                    <p class="font-semibold text-gray-900">{{ $transaksi->tanggal->locale('id')->isoFormat('D MMMM YYYY') }}</p>
                </div>
            </div>

            {{-- Data Siswa --}}
            <div class="bg-emerald-50 rounded-xl p-4 mb-6">
                <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wider mb-2">Data Siswa</p>
                <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-sm">
                    <div><span class="text-gray-500">Nama:</span> <span class="font-semibold">{{ $transaksi->siswaTahunAjaran->siswa->nama }}</span></div>
                    <div><span class="text-gray-500">No. Induk:</span> <span class="font-mono">{{ $transaksi->siswaTahunAjaran->siswa->no_induk }}</span></div>
                    <div><span class="text-gray-500">Kelas:</span> <span class="font-semibold">{{ $transaksi->siswaTahunAjaran->siswa->kelas }}</span></div>
                    <div><span class="text-gray-500">Tahun Ajaran:</span> <span class="font-semibold">{{ $transaksi->siswaTahunAjaran->tahunAjaran->nama }}</span></div>
                </div>
            </div>

            {{-- Rincian Pembayaran --}}
            <table class="w-full text-sm mb-6">
                <thead>
                    <tr class="border-b-2 border-gray-200">
                        <th class="text-left pb-2 text-gray-600 font-semibold">Keterangan</th>
                        <th class="text-right pb-2 text-gray-600 font-semibold">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaksi->details as $detail)
                        <tr class="border-b border-gray-100">
                            <td class="py-2 text-gray-800">
                                @if($detail->jenis === 'spp')
                                    SPP Bulan {{ \Carbon\Carbon::createFromDate($detail->tahun, $detail->bulan, 1)->locale('id')->isoFormat('MMMM YYYY') }}
                                @elseif($detail->jenis === 'iuran')
                                    {{ $detail->jenisPenerimaan->nama ?? 'Iuran' }}
                                @else
                                    Cicilan Tunggakan
                                @endif
                            </td>
                            <td class="py-2 text-right font-medium">Rp {{ number_format($detail->nominal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-emerald-600">
                        <td class="pt-3 font-bold text-gray-900 text-base">TOTAL</td>
                        <td class="pt-3 text-right font-bold text-emerald-700 text-xl">
                            Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            @if($transaksi->catatan)
                <p class="text-sm text-gray-500 italic mb-4">Catatan: {{ $transaksi->catatan }}</p>
            @endif

            {{-- Tanda tangan --}}
            <div class="flex justify-between items-end mt-8 pt-6 border-t border-gray-200">
                <div class="text-center text-sm">
                    <p class="text-gray-500">Orang Tua / Wali</p>
                    <div class="h-14"></div>
                    <p class="font-semibold text-gray-800 border-t border-gray-400 pt-1 min-w-32">(.............................)</p>
                </div>
                <div class="text-center text-sm">
                    <p class="text-gray-500">Bendahara / Operator</p>
                    <div class="h-14"></div>
                    <p class="font-semibold text-gray-800 border-t border-gray-400 pt-1 min-w-32">{{ $transaksi->user->name }}</p>
                </div>
            </div>
        </div>
    </div>

</x-layouts.app>
