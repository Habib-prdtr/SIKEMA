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
    </div>

    <div class="card max-w-xl">
        <div class="px-6 pt-6 pb-4 border-b border-gray-200 text-center">
            <h2 class="text-lg font-bold text-gray-900">{{ $sekolah->nama_sekolah ?? 'MTS IHYAUL ULUM' }}</h2>
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

</x-layouts.app>
