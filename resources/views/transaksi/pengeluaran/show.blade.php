<x-layouts.app title="Detail Pengeluaran">
    <x-slot:pageTitle>Pengeluaran / Detail</x-slot:pageTitle>

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
            #bukti-pengeluaran, #bukti-pengeluaran * {
                visibility: visible;
                color: #000000 !important;
                font-weight: 700 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            #bukti-pengeluaran {
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
            Cetak
        </button>
    </div>

    <div class="card max-w-xl mx-auto border-2 border-black p-4 sm:p-5 text-black font-bold" id="bukti-pengeluaran">
        {{-- Kop --}}
        <div class="pb-3 border-b-2 border-black text-center">
            <h2 class="text-lg font-black text-black uppercase tracking-wider leading-tight">{{ $sekolah->nama_sekolah ?? 'MTS IHYAUL ULUM' }}</h2>
            <div class="mt-2 inline-block bg-black text-white px-4 py-0.5 rounded text-xs font-black tracking-widest uppercase bg-black-print">
                BUKTI PENGELUARAN KAS
            </div>
        </div>

        <div class="pt-3 space-y-3">
            <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs font-bold border-b border-black pb-2">
                <div>
                    <p class="text-black">Tanggal:</p>
                    <p class="font-black text-black">{{ $pengeluaran->tanggal->locale('id')->isoFormat('D MMMM YYYY') }}</p>
                </div>
                <div>
                    <p class="text-black">Pos Biaya:</p>
                    <p class="font-black text-black">{{ $pengeluaran->posBiaya->nama }}</p>
                </div>
                <div>
                    <p class="text-black">Keterangan:</p>
                    <p class="font-black text-black">{{ $pengeluaran->keterangan }}</p>
                </div>
                <div>
                    <p class="text-black">Operator:</p>
                    <p class="font-black text-black">{{ $pengeluaran->user->name }}</p>
                </div>
            </div>

            <div class="p-2.5 bg-gray-50 border border-black rounded-lg flex items-center justify-between">
                <p class="font-black text-black text-xs uppercase">Total Pengeluaran</p>
                <p class="text-xl font-black text-black">{{ format_rupiah($pengeluaran->jumlah) }}</p>
            </div>

            <div class="flex justify-between items-end pt-3 border-t border-black">
                <div class="text-center text-xs font-bold">
                    <p class="text-black font-bold">Disetujui oleh</p>
                    <div class="h-10"></div>
                    <p class="border-t border-black pt-0.5 min-w-28 font-black text-black">(.....................)</p>
                </div>
                <div class="text-center text-xs font-bold">
                    <p class="text-black font-bold">Bendahara</p>
                    <div class="h-10"></div>
                    <p class="border-t border-black pt-0.5 font-black text-black">{{ $pengeluaran->user->name }}</p>
                </div>
            </div>
        </div>
    </div>

</x-layouts.app>
