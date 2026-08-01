<header class="no-print bg-white border-b border-gray-200 px-4 sm:px-6 py-3 flex items-center justify-between gap-4 sticky top-0 z-30">

    {{-- Mobile sidebar toggle --}}
    <button id="sidebar-toggle" class="lg:hidden text-gray-500 hover:text-gray-700 hover:bg-gray-100 p-2 rounded-lg transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    {{-- Page title (breadcrumb) --}}
    <div class="flex-1 min-w-0">
        @isset($pageTitle)
            <h2 class="text-base font-semibold text-gray-900 truncate">{{ $pageTitle }}</h2>
        @endisset
    </div>

    {{-- Right side: jenjang kelas + tahun ajaran aktif + user --}}
    <div class="flex items-center gap-3">

        @php
            $tahunAktif = \App\Models\TahunAjaran::aktif();
            $jenjangAktif = \App\Models\Sekolah::getJenjangAktif();
            $labelJenjang = match($jenjangAktif) {
                '7' => 'Kelas 7',
                '8' => 'Kelas 8',
                '9' => 'Kelas 9',
                default => 'Semua Kelas',
            };
        @endphp

        {{-- Badge Jenjang Kelas Aktif --}}
        <a href="{{ route('pengaturan.sekolah.edit') }}" title="Klik untuk mengubah fokus jenjang kelas"
            class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold text-teal-800 bg-teal-50 hover:bg-teal-100 border border-teal-200 px-3 py-1.5 rounded-full transition-colors cursor-pointer">
            <span class="w-1.5 h-1.5 bg-teal-500 rounded-full animate-pulse"></span>
            Fokus: {{ $labelJenjang }}
        </a>

        @if($tahunAktif)
            <span class="hidden sm:inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-full">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                TA {{ $tahunAktif->nama }}
            </span>
        @else
            <span class="hidden sm:inline-flex items-center gap-1.5 text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-full">
                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                Belum ada TA aktif
            </span>
        @endif

        <div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center">
            <span class="text-xs font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
        </div>
    </div>

</header>
