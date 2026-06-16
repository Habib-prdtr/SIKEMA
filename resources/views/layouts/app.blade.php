<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SIKEMA Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    .hidden {
        display: none !important;
    }
    </style>
</head>

<body>

    <div class="d-flex">

        <!-- SIDEBAR -->
        <div id="app-sidebar" class="bg-dark text-white p-3 h-100 position-fixed"
            style="width: 250px; transform: translateX(-100%); transition: transform 0.3s ease;">

            <h4>SIKEMA</h4>
            <hr>

            <a href="/dashboard" class="text-white d-block">Dashboard</a>
            <a href="/students" class="text-white d-block">Data Siswa</a>
            <a href="/payments" class="text-white d-block">Pembayaran</a>
            <a href="/reports" class="text-white d-block">Laporan</a>

        </div>

        <!-- OVERLAY -->
        <div id="sidebar-overlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark opacity-50 hidden"></div>

        <!-- MAIN CONTENT -->
        <div class="p-4 w-100">

            <!-- TOGGLE BUTTON -->
            <button id="sidebar-toggle" class="btn btn-dark mb-3">
                ☰
            </button>

            @yield('content')

        </div>

    </div>

    <script src="{{ asset('js/app.js') }}"></script>

</body>

</html>