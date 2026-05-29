<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login' }} — SIKEMA</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-emerald-50 flex items-center justify-center p-4 overflow-hidden">

    {{-- Islamic Geometric Background --}}
    <div class="absolute inset-0 opacity-[0.04] pointer-events-none">
        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="islamic-pattern" x="0" y="0" width="80" height="80" patternUnits="userSpaceOnUse">
                    <circle cx="40" cy="40" r="28" fill="none" stroke="#059669" stroke-width="1"/>
                    <circle cx="40" cy="40" r="18" fill="none" stroke="#059669" stroke-width="1"/>
                    <circle cx="40" cy="40" r="8"  fill="none" stroke="#059669" stroke-width="1"/>
                    <path d="M 40 12 L 40 68 M 12 40 L 68 40 M 20 20 L 60 60 M 60 20 L 20 60" stroke="#059669" stroke-width="0.5"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#islamic-pattern)"/>
        </svg>
    </div>

    <div class="w-full max-w-md relative z-10">
        {{ $slot }}
    </div>

</body>
</html>
