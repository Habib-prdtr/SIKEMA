<x-layouts.app title="Detail Pengeluaran">
    <x-slot:pageTitle>Pengeluaran / Detail</x-slot:pageTitle>

    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            .no-print {
                display: none !important;
            }
            .card {
                border: 1px solid #000000 !important;
                box-shadow: none !important;
                background: white !important;
                color: black !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .bg-red-600 {
                background-color: #ffffff !important;
                color: #000000 !important;
                border: 1px solid #000000 !important;
            }
            .bg-red-50 {
                background-color: #f3f4f6 !important;
                border: 1px solid #d1d5db !important;
            }
            .text-red-800, .text-red-700 {
                color: #000000 !important;
            }
            .text-gray-500 {
                color: #374151 !important;
            }
            .text-gray-900 {
                color: #000000 !important;
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
        <button id="btn-print" class="btn-primary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak
        </button>
        <button id="btn-print-thermal" class="btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Cetak Thermal (58mm)
        </button>
    </div>

    <div class="card max-w-xl" id="kwitansi">
        <div class="px-6 pt-6 pb-4 border-b border-gray-200 text-center">
            <h2 class="text-lg font-bold text-gray-900">{{ $sekolah->nama_sekolah ?? 'MADRASAH' }}</h2>
            <div class="mt-2 inline-block bg-red-600 text-white px-5 py-1 rounded-full text-xs font-semibold tracking-wide">
                BUKTI PENGELUARAN KAS
            </div>
        </div>

        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Tanggal</p>
                    <p class="font-semibold">{{ $pengeluaran->tanggal->locale('id')->isoFormat('D MMMM YYYY') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Pos Biaya</p>
                    <p class="font-semibold">{{ $pengeluaran->posBiaya->nama }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Keterangan</p>
                    <p class="font-semibold">{{ $pengeluaran->keterangan }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Operator</p>
                    <p class="font-semibold">{{ $pengeluaran->user->name }}</p>
                </div>
            </div>

            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center justify-between">
                <p class="font-semibold text-red-800">Total Pengeluaran</p>
                <p class="text-2xl font-bold text-red-700">{{ format_rupiah($pengeluaran->jumlah) }}</p>
            </div>

            <div class="flex justify-between items-end mt-6 pt-4 border-t border-gray-200">
                <div class="text-center text-sm">
                    <p class="text-gray-500">Disetujui oleh</p>
                    <div class="h-12"></div>
                    <p class="border-t border-gray-400 pt-1 min-w-28 font-semibold text-gray-700">(.....................)</p>
                </div>
                <div class="text-center text-sm">
                    <p class="text-gray-500">Bendahara</p>
                    <div class="h-12"></div>
                    <p class="border-t border-gray-400 pt-1 font-semibold text-gray-700">{{ $pengeluaran->user->name }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('btn-print-thermal')?.addEventListener('click', function() {
            const styleId = 'thermal-print-styles';
            let styleTag = document.getElementById(styleId);
            
            if (!styleTag) {
                styleTag = document.createElement('style');
                styleTag.id = styleId;
                styleTag.innerHTML = `
                    @media print {
                        @page {
                            size: 58mm auto;
                            margin: 0;
                        }
                        
                        /* Hide non-receipt elements */
                        aside, 
                        header, 
                        #sidebar-overlay, 
                        .alert-success, 
                        .alert-error {
                            display: none !important;
                        }
                        
                        /* Reset layout wrappers for print */
                        body, 
                        body > div, 
                        body > div > div, 
                        main {
                            margin: 0 !important;
                            padding: 0 !important;
                            height: auto !important;
                            min-height: 0 !important;
                            display: block !important;
                            width: 58mm !important;
                            background: white !important;
                        }
                        
                        body {
                            color: black !important;
                            font-family: 'Courier New', Courier, monospace !important;
                            font-size: 9px !important;
                        }
                        
                        .no-print {
                            display: none !important;
                        }
                        
                        #kwitansi {
                            width: 58mm !important;
                            max-width: 58mm !important;
                            border: none !important;
                            border-radius: 0 !important;
                            box-shadow: none !important;
                            background: white !important;
                            color: black !important;
                            margin: 0 !important;
                            padding: 2mm !important;
                        }
                        
                        #kwitansi * {
                            border-radius: 0 !important;
                        }
                        
                        /* Kop Header */
                        #kwitansi > div:first-child {
                            padding: 2mm 0 !important;
                            border-bottom: 1px dashed #000 !important;
                        }
                        #kwitansi h2 {
                            font-size: 11px !important;
                            font-weight: bold !important;
                            margin: 0 !important;
                        }
                        #kwitansi p {
                            font-size: 8px !important;
                            margin: 2px 0 0 0 !important;
                        }
                        #kwitansi .badge, #kwitansi .rounded, #kwitansi .rounded-full {
                            font-size: 8px !important;
                            padding: 1px 4px !important;
                            margin-top: 4px !important;
                            border: 1px solid #000 !important;
                            background-color: transparent !important;
                            color: #000 !important;
                            border-radius: 2px !important;
                            display: inline-block !important;
                        }
                        
                        /* Details / Info */
                        #kwitansi .p-8, #kwitansi .p-6 {
                            padding: 2mm 0 !important;
                        }
                        
                        /* Flex row layout to column stack */
                        .flex.justify-between.items-start, 
                        .grid.grid-cols-2 {
                            display: block !important;
                            margin-bottom: 4px !important;
                        }
                        .flex.justify-between.items-start > div,
                        .grid.grid-cols-2 > div {
                            text-align: left !important;
                            margin-bottom: 2px !important;
                            font-size: 8px !important;
                        }
                        .font-mono {
                            font-size: 9px !important;
                            font-weight: bold !important;
                        }
                        
                        /* Data Box */
                        .bg-gray-50, .bg-red-50 {
                            background-color: transparent !important;
                            border: 1px dashed #000 !important;
                            border-radius: 0 !important;
                            padding: 4px !important;
                            margin-bottom: 6px !important;
                            display: block !important;
                        }
                        .bg-gray-50 p, .bg-red-50 p {
                            font-size: 8px !important;
                        }
                        
                        /* Cash Out Total Box */
                        #kwitansi .bg-red-50 {
                            display: flex !important;
                            justify-content: space-between !important;
                            align-items: center !important;
                            background-color: transparent !important;
                            border: 1px dashed #000 !important;
                            border-radius: 0 !important;
                            padding: 4px !important;
                            margin-top: 8px !important;
                        }
                        #kwitansi .bg-red-50 p {
                            font-size: 9px !important;
                            margin: 0 !important;
                        }
                        #kwitansi .bg-red-50 p.text-2xl {
                            font-size: 11px !important;
                            font-weight: bold !important;
                        }
                        
                        /* Tables */
                        table {
                            width: 100% !important;
                            margin-bottom: 6px !important;
                            font-size: 8px !important;
                            border-collapse: collapse !important;
                        }
                        table th, table td {
                            padding: 2px 0 !important;
                        }
                        table thead tr {
                            border-bottom: 1px dashed #000 !important;
                        }
                        table tfoot tr {
                            border-top: 1px dashed #000 !important;
                        }
                        table tfoot td {
                            font-size: 9px !important;
                        }
                        table tfoot td.text-xl {
                            font-size: 11px !important;
                        }
                        
                        /* Signatures */
                        .flex.justify-between.items-end {
                            display: flex !important;
                            flex-direction: column !important;
                            align-items: center !important;
                            margin-top: 8px !important;
                            padding-top: 4px !important;
                            border-top: 1px dashed #000 !important;
                            gap: 8px !important;
                        }
                        .flex.justify-between.items-end > div {
                            width: 100% !important;
                            text-align: center !important;
                        }
                        .flex.justify-between.items-end .h-14,
                        .flex.justify-between.items-end .h-12 {
                            height: 24px !important;
                        }
                        .flex.justify-between.items-end p {
                            font-size: 8px !important;
                        }
                        .flex.justify-between.items-end .min-w-32,
                        .flex.justify-between.items-end .min-w-28 {
                            min-width: 80px !important;
                            display: inline-block !important;
                        }
                    }
                `;
                document.head.appendChild(styleTag);
            }
            
            window.print();
        });
        
        window.addEventListener('afterprint', function() {
            const styleTag = document.getElementById('thermal-print-styles');
            if (styleTag) {
                styleTag.remove();
            }
        });
    </script>

</x-layouts.app>
