<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SIKEMA Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="d-flex">

        <!-- SIDEBAR -->
        <div class="bg-dark text-white p-3" style="width: 250px; height: 100vh;">
            <h4>SIKEMA</h4>
            <hr>

            <a href="/dashboard" class="text-white d-block">Dashboard</a>
            <a href="/students" class="text-white d-block">Data Siswa</a>
            <a href="/payments" class="text-white d-block">Pembayaran</a>
            <a href="/reports" class="text-white d-block">Laporan</a>
        </div>

        <!-- MAIN CONTENT -->
        <div class="p-4 w-100">
            @yield('content')
        </div>

    </div>

</body>

</html>