<x-layouts.app title="Detail Transaksi">
    <x-slot:pageTitle>Penerimaan / Detail Transaksi</x-slot:pageTitle>

    <style>
        @media print {
            @page {
                size: 140mm 95mm;
                margin: 0;
            }
            html, body {
                width: 140mm !important;
                height: 95mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                overflow: hidden !important;
            }
            aside, header, nav, #sidebar-overlay, .no-print, [role="navigation"] {
                display: none !important;
            }
            main {
                padding: 0 !important;
                margin: 0 !important;
                overflow: visible !important;
            }
            body * {
                visibility: hidden !important;
            }
            #kwitansi, #kwitansi * {
                visibility: visible !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            #kwitansi {
                position: fixed !important;
                left: 3mm !important;
                top: 3mm !important;
                width: 134mm !important;
                height: 89mm !important;
                margin: 0 !important;
                padding: 8px 12px !important;
                border: 1.5px solid #000000 !important;
                border-radius: 0px !important;
                box-shadow: none !important;
                background: #ffffff !important;
                box-sizing: border-box !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
            }
            #kwitansi .bg-black-print,
            #kwitansi .bg-black-print * {
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
            Cetak Kwitansi (14x9.5 cm)
        </button>
    </div>

    {{-- Kwitansi (14cm x 9.5cm Format) --}}
    <div class="card max-w-[140mm] mx-auto border-2 border-black p-3 text-black font-bold" id="kwitansi">

        {{-- Kop --}}
        <div class="pb-1.5 border-b border-black text-center">
            <h2 class="text-sm font-black text-black uppercase tracking-wide leading-tight">{{ $sekolah->nama_sekolah ?? 'MTS IHYAUL ULUM' }}</h2>
            @if($sekolah->nama_yayasan ?? false)
                <p class="text-[10px] font-bold text-black leading-tight">{{ $sekolah->nama_yayasan }}</p>
            @endif
            @if(($sekolah->alamat ?? false) || ($sekolah->telepon ?? false))
                <p class="text-[9px] font-bold text-black leading-tight">
                    {{ $sekolah->alamat }}
                    @if(($sekolah->alamat ?? false) && ($sekolah->telepon ?? false)) | @endif
                    @if($sekolah->telepon ?? false) Telp: {{ $sekolah->telepon }} @endif
                </p>
            @endif
            <div class="mt-1 inline-block bg-black text-white px-2 py-0.5 rounded text-[9px] font-black tracking-widest uppercase bg-black-print">
                BUKTI PEMBAYARAN / KWITANSI
            </div>
        </div>

        <div class="pt-1.5 space-y-1.5">
            {{-- No Transaksi & Tanggal --}}
            <div class="flex justify-between items-center text-[10px] font-bold border-b border-black pb-1">
                <div>
                    <span class="text-black">No. Trx:</span>
                    <span class="font-mono text-xs font-black text-black ml-1">{{ $transaksi->no_transaksi }}</span>
                </div>
                <div class="text-right">
                    <span class="text-black">Tgl:</span>
                    <span class="font-black text-black ml-1">{{ $transaksi->tanggal->locale('id')->isoFormat('D MMMM YYYY') }}</span>
                </div>
            </div>

            {{-- Data Siswa --}}
            <div class="border border-black rounded p-1.5 bg-gray-50/50">
                <div class="grid grid-cols-2 gap-x-2 gap-y-0.5 text-[10px] font-bold">
                    <div><span class="text-black">Nama:</span> <span class="font-black text-black">{{ $transaksi->siswaTahunAjaran->siswa->nama }}</span></div>
                    <div><span class="text-black">No. Induk:</span> <span class="font-mono font-black text-black">{{ $transaksi->siswaTahunAjaran->siswa->no_induk }}</span></div>
                    <div><span class="text-black">Kelas:</span> <span class="font-black text-black">{{ $transaksi->siswaTahunAjaran->siswa->kelas }}</span></div>
                    <div><span class="text-black">TA:</span> <span class="font-black text-black">{{ $transaksi->siswaTahunAjaran->tahunAjaran->nama }}</span></div>
                </div>
            </div>

            {{-- Rincian Pembayaran --}}
            <table class="w-full text-[10px] font-bold my-1">
                <thead>
                    <tr class="border-b border-black">
                        <th class="text-left py-0.5 text-black font-black">Keterangan</th>
                        <th class="text-right py-0.5 text-black font-black">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300 border-b border-black">
                    @foreach($transaksi->details as $detail)
                        <tr>
                            <td class="py-0.5 text-black font-bold">
                                @if($detail->jenis === 'spp')
                                    SPP {{ \Carbon\Carbon::createFromDate($detail->tahun, $detail->bulan, 1)->locale('id')->isoFormat('MMMM YYYY') }}
                                    @if(($transaksi->siswaTahunAjaran->tarif_spp ?? 0) > 0 && $detail->nominal < $transaksi->siswaTahunAjaran->tarif_spp)
                                        <span class="text-[9px] text-black font-bold ml-1">(Disp: {{ $transaksi->siswaTahunAjaran->dispensasi->nama ?? 'Potongan' }})</span>
                                    @endif
                                @elseif($detail->jenis === 'iuran')
                                    {{ $detail->jenisPenerimaan->nama ?? 'Iuran' }}
                                @else
                                    Cicilan Tunggakan
                                @endif
                            </td>
                            <td class="py-0.5 text-right font-black text-black">{{ format_rupiah($detail->nominal) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="pt-1 font-black text-black text-xs">TOTAL BAYAR</td>
                        <td class="pt-1 text-right font-black text-black text-xs">
                            {{ format_rupiah($transaksi->total_bayar) }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            @if($transaksi->catatan)
                <p class="text-[9px] font-bold text-black italic">Catatan: {{ $transaksi->catatan }}</p>
            @endif

            {{-- Tanda tangan --}}
            <div class="flex justify-end items-end pt-1 border-t border-black">
                <div class="text-center text-[9px] font-bold">
                    <p class="text-black font-bold">Kepala TU / Kasir</p>
                    <div class="h-6"></div>
                    <p class="font-black text-black border-t border-black pt-0.5 min-w-24">
                        {{ !empty($sekolah->kepala_tu) ? $sekolah->kepala_tu : $transaksi->user->name }}
                    </p>
                </div>
            </div>
        </div>
    </div>

</x-layouts.app>
