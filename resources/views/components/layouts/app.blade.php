@props(['title' => 'Dashboard'])
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} — SIKEMA</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 font-sans">

<div class="flex h-full min-h-screen">

    {{-- ── Sidebar Overlay (mobile) ─────────────────────── --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/40 z-40 hidden lg:hidden"></div>

    {{-- ── Sidebar ──────────────────────────────────────── --}}
    @include('components.sidebar')

    {{-- ── Main ────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Topbar --}}
        @include('components.topbar', ['pageTitle' => $pageTitle ?? null])

        {{-- Content --}}
        <main class="flex-1 p-4 sm:p-6 overflow-y-auto">

            {{-- Alert sukses --}}
            @if(session('sukses'))
                <div data-auto-dismiss="4000" class="alert-success mb-5">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('sukses') }}</span>
                </div>
            @endif

            {{-- Alert error --}}
            @if(session('error') || $errors->any())
                <div data-auto-dismiss="6000" class="alert-error mb-5">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <div>
                        @if(session('error'))
                            <p>{{ session('error') }}</p>
                        @endif
                        @if($errors->any())
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endif

            {{ $slot }}
        </main>

    </div>
</div>

</body>
</html>
