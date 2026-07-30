<x-layouts.app title="Detail Transaksi">
    <x-slot:pageTitle>Penerimaan / Detail Transaksi</x-slot:pageTitle>

    <style>
        @media print {
            @page {
                size: auto;
                margin: 5mm;
            }
            aside, header, nav, #sidebar-overlay, .no-print, [role="navigation"] {
                display: none !important;
            }
            body, html {
                background: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
                height: auto !important;
                width: 100% !important;
            }
            main {
                padding: 0 !important;
                margin: 0 !important;
                overflow: visible !important;
            }
            body * {
                visibility: hidden;
            }
            #kwitansi, #kwitansi * {
                visibility: visible;
                color: #000000 !important;
                font-weight: 700 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            #kwitansi {
                position: relative !important;
                left: auto !important;
                top: auto !important;
                width: 165mm !important;
                max-width: 100% !important;
                margin: 0 auto !important;
                padding: 12px 18px !important;
                border: 2px solid #000000 !important;
                border-radius: 4px !important;
                box-shadow: none !important;
                background: #ffffff !important;
            }
            .bg-black-print {
                background-color: #000000 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>

    {{-- Tombol aksi (no-print) --}}
    <div class="flex items-center gap-3 mb-5 no-print">
        <a href="{{ route('penerimaan.index') }}" class="btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
        <button id="btn-print" class="btn-primary btn-sm" onclick="window.print()">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak Kwitansi
        </button>
    </div>

    {{-- Kwitansi --}}
    <div class="card max-w-xl mx-auto border-2 border-black p-4 sm:p-5 text-black font-bold" id="kwitansi">

        {{-- Kop --}}
        <div class="pb-3 border-b-2 border-black text-center">
            <h2 class="text-lg font-black text-black uppercase tracking-wider leading-tight">{{ $sekolah->nama_sekolah ?? 'MTS IHYAUL ULUM' }}</h2>
            @if($sekolah->nama_yayasan ?? false)
                <p class="text-xs font-bold text-black leading-tight">{{ $sekolah->nama_yayasan }}</p>
            @endif
            @if(($sekolah->alamat ?? false) || ($sekolah->telepon ?? false))
                <p class="text-[11px] font-bold text-black mt-0.5 leading-tight">
                    {{ $sekolah->alamat }}
                    @if(($sekolah->alamat ?? false) && ($sekolah->telepon ?? false)) | @endif
                    @if($sekolah->telepon ?? false) Telp: {{ $sekolah->telepon }} @endif
                </p>
            @endif
            <div class="mt-2 inline-block bg-black text-white px-4 py-0.5 rounded text-xs font-black tracking-widest uppercase bg-black-print">
                BUKTI PEMBAYARAN / KWITANSI
            </div>
        </div>

        <div class="pt-3 space-y-3">
            {{-- No Transaksi & Tanggal --}}
            <div class="flex justify-between items-center text-xs font-bold border-b border-black pb-2">
                <div>
                    <span class="text-black">No. Transaksi:</span>
                    <span class="font-mono text-sm font-black text-black ml-1">{{ $transaksi->no_transaksi }}</span>
                </div>
                <div class="text-right">
                    <span class="text-black">Tanggal:</span>
                    <span class="font-black text-black ml-1">{{ $transaksi->tanggal->locale('id')->isoFormat('D MMMM YYYY') }}</span>
                </div>
            </div>

            {{-- Data Siswa --}}
            <div class="border border-black rounded-lg p-2.5 bg-gray-50/50">
                <p class="text-[11px] font-black text-black uppercase tracking-wider mb-1">Data Siswa</p>
                <div class="grid grid-cols-2 gap-x-4 gap-y-0.5 text-xs font-bold">
                    <div><span class="text-black">Nama:</span> <span class="font-black text-black">{{ $transaksi->siswaTahunAjaran->siswa->nama }}</span></div>
                    <div><span class="text-black">No. Induk:</span> <span class="font-mono font-black text-black">{{ $transaksi->siswaTahunAjaran->siswa->no_induk }}</span></div>
                    <div><span class="text-black">Kelas:</span> <span class="font-black text-black">{{ $transaksi->siswaTahunAjaran->siswa->kelas }}</span></div>
                    <div><span class="text-black">Tahun Ajaran:</span> <span class="font-black text-black">{{ $transaksi->siswaTahunAjaran->tahunAjaran->nama }}</span></div>
                </div>
            </div>

            {{-- Rincian Pembayaran --}}
            <table class="w-full text-xs font-bold my-2">
                <thead>
                    <tr class="border-b-2 border-black">
                        <th class="text-left py-1 text-black font-black">Keterangan</th>
                        <th class="text-right py-1 text-black font-black">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300 border-b-2 border-black">
                    @foreach($transaksi->details as $detail)
                        <tr>
                            <td class="py-1 text-black font-bold">
                                @if($detail->jenis === 'spp')
                                    SPP Bulan {{ \Carbon\Carbon::createFromDate($detail->tahun, $detail->bulan, 1)->locale('id')->isoFormat('MMMM YYYY') }}
                                    @if(($transaksi->siswaTahunAjaran->tarif_spp ?? 0) > 0 && $detail->nominal < $transaksi->siswaTahunAjaran->tarif_spp)
                                        <span class="text-[11px] text-black font-bold ml-1">(Dispensasi: {{ $transaksi->siswaTahunAjaran->dispensasi->nama ?? 'Potongan' }})</span>
                                    @endif
                                @elseif($detail->jenis === 'iuran')
                                    {{ $detail->jenisPenerimaan->nama ?? 'Iuran' }}
                                @else
                                    Cicilan Tunggakan
                                @endif
                            </td>
                            <td class="py-1 text-right font-black text-black">{{ format_rupiah($detail->nominal) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="pt-2 font-black text-black text-sm">TOTAL BAYAR</td>
                        <td class="pt-2 text-right font-black text-black text-base">
                            {{ format_rupiah($transaksi->total_bayar) }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            @if($transaksi->catatan)
                <p class="text-xs font-bold text-black italic">Catatan: {{ $transaksi->catatan }}</p>
            @endif

            {{-- Tanda tangan --}}
            <div class="flex justify-end items-end pt-3 border-t border-black">
                <div class="text-center text-xs font-bold">
                    <p class="text-black font-bold">Kepala TU / Kasir</p>
                    <div class="h-10"></div>
                    <p class="font-black text-black border-t border-black pt-0.5 min-w-32">
                        {{ !empty($sekolah->kepala_tu) ? $sekolah->kepala_tu : $transaksi->user->name }}
                    </p>
                    @if(!empty($sekolah->nip_kepala_tu))
                        <p class="text-[10px] font-bold text-black">NIP. {{ $sekolah->nip_kepala_tu }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-layouts.app>
