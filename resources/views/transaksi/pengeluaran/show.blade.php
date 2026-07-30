<x-layouts.app title="Detail Pengeluaran">
    <x-slot:pageTitle>Pengeluaran / Detail</x-slot:pageTitle>

    <style>
        @media print {
            @page {
                size: 95mm 140mm;
                margin: 0;
            }
            html, body {
                width: 95mm !important;
                height: 140mm !important;
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
            #bukti-pengeluaran, #bukti-pengeluaran * {
                visibility: visible !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            #bukti-pengeluaran {
                position: fixed !important;
                left: 3mm !important;
                top: 6mm !important;
                width: 89mm !important;
                height: 130mm !important;
                margin: 0 !important;
                padding: 10px 12px !important;
                border: 1.5px solid #000000 !important;
                border-radius: 0px !important;
                box-shadow: none !important;
                background: #ffffff !important;
                box-sizing: border-box !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: flex-start !important;
            }
        }
    </style>

    <div class="flex items-center gap-3 mb-5 no-print">
        <a href="{{ route('pengeluaran.index') }}" class="btn-secondary btn-sm">
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
            Cetak (9.5x14 cm Portrait)
        </button>
    </div>

    <div class="card max-w-[95mm] mx-auto border-2 border-black p-3 text-black font-bold flex flex-col justify-start min-h-[134mm]" id="bukti-pengeluaran">
        {{-- Kop --}}
        <div class="pb-2 border-b border-black text-center shrink-0">
            <h2 style="font-size: 8.5px; line-height: 1.2;" class="font-black text-black uppercase tracking-wide">{{ $sekolah->nama_sekolah ?? 'MTS IHYAUL ULUM' }}</h2>
            @if($sekolah->nama_yayasan ?? false)
                <p style="font-size: 8.5px; line-height: 1.2;" class="font-bold text-black mt-0.5">{{ $sekolah->nama_yayasan }}</p>
            @endif
            @if(($sekolah->alamat ?? false) || ($sekolah->telepon ?? false))
                <p style="font-size: 8.5px; line-height: 1.2;" class="font-bold text-black mt-0.5">
                    {{ $sekolah->alamat }}
                    @if(($sekolah->alamat ?? false) && ($sekolah->telepon ?? false)) | @endif
                    @if($sekolah->telepon ?? false) Telp: {{ $sekolah->telepon }} @endif
                </p>
            @endif
            <div class="mt-1.5 inline-block border border-black text-black px-2 py-0.5 rounded text-[9px] font-black tracking-widest uppercase">
                BUKTI PENGELUARAN KAS
            </div>
        </div>

        <div class="border-b border-black py-2 my-1 shrink-0 text-[10px] font-bold space-y-1">
            <div class="flex items-baseline justify-between gap-2">
                <div><span class="text-black">Tanggal:</span> <span class="font-black text-black ml-1">{{ $pengeluaran->tanggal->locale('id')->isoFormat('D MMMM YYYY') }}</span></div>
                <div class="shrink-0 text-right"><span class="text-black">Pos Biaya:</span> <span class="font-black text-black ml-1">{{ $pengeluaran->posBiaya->nama }}</span></div>
            </div>
            <div class="flex items-baseline justify-between gap-2">
                <div class="truncate"><span class="text-black">Keterangan:</span> <span class="font-black text-black ml-1">{{ $pengeluaran->keterangan }}</span></div>
                <div class="shrink-0 text-right"><span class="text-black">Operator:</span> <span class="font-black text-black ml-1">{{ $pengeluaran->user->name }}</span></div>
            </div>
        </div>

        <div class="p-2 bg-gray-50 border border-black rounded flex items-center justify-between my-2 shrink-0">
            <p class="font-black text-black text-[10px] uppercase">Total Pengeluaran</p>
            <p class="text-sm font-black text-black">{{ format_rupiah($pengeluaran->jumlah) }}</p>
        </div>

        <div class="flex justify-between items-end pt-2 mt-auto shrink-0">
            <div class="text-center">
                <p style="font-size: 9px; line-height: 1.2;" class="text-black font-semibold">Disetujui oleh</p>
                <div class="h-7"></div>
                <p style="font-size: 9.5px; line-height: 1.2;" class="border-t border-black pt-0.5 min-w-20 font-bold text-black">(.....................)</p>
            </div>
            <div class="text-center">
                <p style="font-size: 9px; line-height: 1.2;" class="text-black font-semibold">Bendahara</p>
                <div class="h-7"></div>
                <p style="font-size: 9.5px; line-height: 1.2;" class="border-t border-black pt-0.5 min-w-20 font-bold text-black">{{ $pengeluaran->user->name }}</p>
            </div>
        </div>
    </div>

</x-layouts.app>
