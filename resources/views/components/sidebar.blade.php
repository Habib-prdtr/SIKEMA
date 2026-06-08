@php
    $current = request()->route()->getName() ?? '';

    $navItems = [
        'master' => [
            'label' => 'Master Data',
            'icon'  => 'database',
            'id'    => 'menu-master',
            'items' => [
                ['label' => 'Tahun Ajaran',          'route' => 'master.tahun-ajaran.index'],
                ['label' => 'Data Siswa',             'route' => 'master.siswa.index'],
                ['label' => 'Tarif SPP',              'route' => 'master.tarif-spp.index'],
                ['label' => 'Siswa per Tahun Ajaran', 'route' => 'master.siswa-tahun-ajaran.index'],
                ['label' => 'Jenis Penerimaan',       'route' => 'master.jenis-penerimaan.index'],
                ['label' => 'Pos Biaya',              'route' => 'master.pos-biaya.index'],
                ['label' => 'Saldo Awal',             'route' => 'master.saldo-awal.index'],
            ],
        ],
        'penerimaan' => [
            'label' => 'Penerimaan',
            'icon'  => 'trending-up',
            'id'    => 'menu-penerimaan',
            'items' => [
                ['label' => 'Pencatatan', 'route' => 'penerimaan.catat'],
                ['label' => 'Riwayat',    'route' => 'penerimaan.index'],
                ['label' => 'Laporan',    'route' => 'laporan.penerimaan'],
            ],
        ],
        'pengeluaran' => [
            'label' => 'Pengeluaran',
            'icon'  => 'trending-down',
            'id'    => 'menu-pengeluaran',
            'items' => [
                ['label' => 'Pencatatan', 'route' => 'pengeluaran.catat'],
                ['label' => 'Riwayat',    'route' => 'pengeluaran.index'],
                ['label' => 'Laporan',    'route' => 'laporan.pengeluaran'],
            ],
        ],
    ];
@endphp

<aside id="app-sidebar"
    class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 flex flex-col
           -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out shadow-sm">

    {{-- Logo --}}
    <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3">
        <div class="w-9 h-9 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-sm shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
            </svg>
        </div>
        <div>
            <h1 class="font-bold text-gray-900 text-base leading-tight">SIKEMA</h1>
            <p class="text-xs text-gray-500">Sistem Keuangan Madrasah</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-3 px-3 space-y-0.5">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                   {{ str_starts_with($current, 'dashboard') ? 'nav-item-active' : 'nav-item' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        {{-- Master Data --}}
        @php $masterActive = str_starts_with($current, 'master.'); @endphp
        <div>
            <button data-submenu-toggle="menu-master"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                       {{ $masterActive ? 'nav-item-active' : 'nav-item' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                    <span>Master Data</span>
                </div>
                <svg data-chevron class="w-4 h-4 transition-transform {{ $masterActive ? 'rotate-0' : '-rotate-90' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="menu-master" class="{{ $masterActive ? '' : 'hidden' }} ml-4 mt-1 pl-4 border-l-2 border-gray-200 space-y-0.5">
                @foreach($navItems['master']['items'] as $item)
                    <a href="{{ route($item['route']) }}"
                        class="block px-3 py-2 rounded-lg text-sm transition-colors
                               {{ $current === $item['route'] ? 'nav-sub-active' : 'nav-sub' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Penerimaan --}}
        @php $penerimaanActive = str_starts_with($current, 'penerimaan.') || $current === 'laporan.penerimaan'; @endphp
        <div>
            <button data-submenu-toggle="menu-penerimaan"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                       {{ $penerimaanActive ? 'nav-item-active' : 'nav-item' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span>Penerimaan</span>
                </div>
                <svg data-chevron class="w-4 h-4 transition-transform {{ $penerimaanActive ? 'rotate-0' : '-rotate-90' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="menu-penerimaan" class="{{ $penerimaanActive ? '' : 'hidden' }} ml-4 mt-1 pl-4 border-l-2 border-gray-200 space-y-0.5">
                @foreach($navItems['penerimaan']['items'] as $item)
                    <a href="{{ route($item['route']) }}"
                        class="block px-3 py-2 rounded-lg text-sm transition-colors
                               {{ $current === $item['route'] ? 'nav-sub-active' : 'nav-sub' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Pengeluaran --}}
        @php $pengeluaranActive = str_starts_with($current, 'pengeluaran.') || $current === 'laporan.pengeluaran'; @endphp
        <div>
            <button data-submenu-toggle="menu-pengeluaran"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                       {{ $pengeluaranActive ? 'nav-item-active' : 'nav-item' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 17H5m0 0V9m0 8l8-8 4 4 6-6"/>
                    </svg>
                    <span>Pengeluaran</span>
                </div>
                <svg data-chevron class="w-4 h-4 transition-transform {{ $pengeluaranActive ? 'rotate-0' : '-rotate-90' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="menu-pengeluaran" class="{{ $pengeluaranActive ? '' : 'hidden' }} ml-4 mt-1 pl-4 border-l-2 border-gray-200 space-y-0.5">
                @foreach($navItems['pengeluaran']['items'] as $item)
                    <a href="{{ route($item['route']) }}"
                        class="block px-3 py-2 rounded-lg text-sm transition-colors
                               {{ $current === $item['route'] ? 'nav-sub-active' : 'nav-sub' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Pengaturan --}}
        <a href="{{ route('pengaturan.sekolah.edit') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                   {{ str_starts_with($current, 'pengaturan.') ? 'nav-item-active' : 'nav-item' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Pengaturan
        </a>

    </nav>

    {{-- User info + logout --}}
    <div class="border-t border-gray-200 p-3">
        <div class="flex items-center gap-3 px-3 py-2">
            <div class="w-9 h-9 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center shrink-0">
                <span class="text-sm font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->username }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Logout"
                    class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
