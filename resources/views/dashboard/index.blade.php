@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SIKEMA</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Chart JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
    * {
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: #f5f7fb;
        overflow-x: hidden;
    }

    .sidebar {
        width: 300px;
        height: 100vh;
        background: #fff;
        border-right: 1px solid #e5e7eb;
        position: fixed;
        top: 0;
        left: 0;
        overflow-y: auto;
    }

    .sidebar-header {
        padding: 30px;
        border-bottom: 1px solid #e5e7eb;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .brand-logo {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: linear-gradient(135deg, #10b981, #059669);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.25);
    }

    .brand-logo i {
        color: white;
        font-size: 28px;
    }

    .brand-text h3 {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        color: #0f172a;
    }

    .brand-text p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    .sidebar-menu {
        padding: 15px;
    }

    .menu-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 18px;
        border-radius: 14px;
        margin-bottom: 12px;
        text-decoration: none;
        color: #1e293b;
        font-weight: 500;
        transition: 0.3s;
    }

    .menu-item:hover {
        background: #ecfdf5;
        color: #059669;
    }

    .menu-item.active {
        background: #dcfce7;
        color: #059669;
    }

    .menu-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .menu-left i {
        font-size: 22px;
    }

    .submenu {
        margin-left: 42px;
        border-left: 2px solid #e2e8f0;
        padding-left: 20px;
        margin-top: 5px;
        margin-bottom: 20px;
    }

    .submenu a {
        display: block;
        text-decoration: none;
        color: #475569;
        margin-bottom: 22px;
        font-weight: 500;
        transition: 0.3s;
    }

    .submenu a:hover {
        color: #059669;
    }

    .main-content {
        margin-left: 300px;
    }

    .topbar {
        height: 80px;
        background: white;
        border-bottom: 1px solid #e5e7eb;
        padding: 0 40px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .topbar-title {
        font-size: 18px;
        font-weight: 600;
        color: #0f172a;
    }

    .topbar-right {
        display: flex;
        align-items: center;
        gap: 30px;
    }

    .search-box {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #475569;
        font-size: 17px;
    }

    .topbar-icon {
        position: relative;
        font-size: 24px;
        color: #475569;
        cursor: pointer;
    }

    .notification-dot {
        width: 10px;
        height: 10px;
        background: #ef4444;
        border-radius: 50%;
        position: absolute;
        top: 0;
        right: -2px;
    }

    .logout {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
        color: #1e293b;
        cursor: pointer;
    }

    .content {
        padding: 40px;
    }

    .dashboard-title h1 {
        font-size: 56px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 10px;
    }

    .dashboard-title p {
        font-size: 20px;
        color: #475569;
    }

    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        height: 100%;
    }

    .stat-icon {
        width: 72px;
        height: 72px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        font-size: 32px;
    }

    .bg-blue-soft {
        background: #dbeafe;
        color: #2563eb;
    }

    .bg-green-soft {
        background: #d1fae5;
        color: #059669;
    }

    .bg-red-soft {
        background: #fee2e2;
        color: #dc2626;
    }

    .bg-yellow-soft {
        background: #fef3c7;
        color: #d97706;
    }

    .bg-purple-soft {
        background: #ede9fe;
        color: #9333ea;
    }

    .stat-card h2 {
        font-size: 34px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .stat-card p {
        color: #64748b;
        margin: 0;
    }

    .menu-title {
        font-size: 42px;
        font-weight: 700;
        margin-bottom: 30px;
        color: #0f172a;
    }

    .menu-card {
        background: white;
        border-radius: 22px;
        padding: 32px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.04);
        transition: 0.3s;
        height: 100%;
    }

    .menu-card:hover {
        transform: translateY(-6px);
    }

    .menu-icon {
        width: 80px;
        height: 80px;
        border-radius: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        font-size: 34px;
        color: white;
    }

    .bg-blue {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    .bg-green {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .bg-red {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .bg-gray {
        background: linear-gradient(135deg, #64748b, #475569);
    }

    .menu-card h4 {
        font-weight: 700;
        margin-bottom: 12px;
        color: #0f172a;
        font-size: 24px;
    }

    .menu-card p {
        color: #64748b;
        margin-bottom: 24px;
        font-size: 17px;
    }

    .menu-card a {
        text-decoration: none;
        color: #059669;
        font-weight: 600;
        font-size: 17px;
    }

    /* =========================
        CHART CARD
    ========================== */

    .chart-card {
        background: white;
        border-radius: 24px;
        padding: 30px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.04);
        height: 100%;
    }

    .chart-card h3 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 25px;
        color: #0f172a;
    }

    .payment-info {
        display: flex;
        justify-content: space-around;
        margin-top: 30px;
    }

    .payment-box {
        text-align: center;
    }

    .payment-box h4 {
        font-size: 16px;
        margin-bottom: 8px;
    }

    .payment-box p {
        font-size: 20px;
        font-weight: 700;
        margin: 0;
    }

    .green {
        color: #16a34a;
    }

    .orange {
        color: #f59e0b;
    }

    @media(max-width: 991px) {

        .sidebar {
            width: 100%;
            height: auto;
            position: relative;
        }

        .main-content {
            margin-left: 0;
        }

        .topbar {
            padding: 20px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .content {
            padding: 20px;
        }

        .dashboard-title h1 {
            font-size: 36px;
        }

    }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">

        <div class="sidebar-header">

            <div class="brand">

                <div class="brand-logo">
                    <i class="bi bi-mortarboard"></i>
                </div>

                <div class="brand-text">
                    <h3>SIKEMA</h3>
                    <p>Sistem Keuangan</p>
                </div>

            </div>

        </div>

        <div class="sidebar-menu">

            <a href="#" class="menu-item active">

                <div class="menu-left">
                    <i class="bi bi-house-door"></i>
                    <span>Dashboard</span>
                </div>

            </a>

            <a href="#" class="menu-item">

                <div class="menu-left">
                    <i class="bi bi-database"></i>
                    <span>Data</span>
                </div>

                <i class="bi bi-chevron-right"></i>

            </a>

            <div class="submenu">
                <a href="/data/tahun-ajaran">Tahun Ajaran</a>
                <a href="/data/siswa">Data Siswa</a>
                <a href="#">Siswa per Tahun Ajaran</a>
                <a href="#">Jenis Penerimaan</a>
                <a href="#">Pos Biaya</a>
                <a href="#">Rekap Pembayaran</a>
            </div>

            <a href="#" class="menu-item">

                <div class="menu-left">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Penerimaan</span>
                </div>

            </a>

            <a href="#" class="menu-item">

                <div class="menu-left">
                    <i class="bi bi-graph-down-arrow"></i>
                    <span>Pengeluaran</span>
                </div>

            </a>

            <a href="#" class="menu-item">

                <div class="menu-left">
                    <i class="bi bi-gear"></i>
                    <span>Pengaturan</span>
                </div>

            </a>

        </div>

    </div>

    <!-- MAIN -->
    <div class="main-content">

        <!-- TOPBAR -->
        <div class="topbar">

            <div class="topbar-title">
                Dashboard
            </div>

            <div class="topbar-right">

                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <span>Cari...</span>
                </div>

                <div class="topbar-icon">
                    <i class="bi bi-bell"></i>
                    <div class="notification-dot"></div>
                </div>

                <div class="logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Keluar</span>
                </div>

            </div>

        </div>

        <!-- CONTENT -->
        <div class="content">

            <!-- TITLE -->
            <div class="dashboard-title mb-5">
                <h1>Selamat Datang di SIKEMA</h1>

                <p>
                    Sistem Keuangan Madrasah - Dashboard Utama
                </p>
            </div>

            <!-- STAT -->
            <div class="row g-4 mb-5">

                <div class="col-lg-2 col-md-6">
                    <div class="stat-card">

                        <div class="stat-icon bg-blue-soft">
                            <i class="bi bi-people"></i>
                        </div>

                        <p>Total Siswa</p>
                        <h2>342</h2>

                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <div class="stat-card">

                        <div class="stat-icon bg-green-soft">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>

                        <p>Total Pemasukan</p>
                        <h2 class="text-success">Rp 55jt</h2>

                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <div class="stat-card">

                        <div class="stat-icon bg-red-soft">
                            <i class="bi bi-graph-down-arrow"></i>
                        </div>

                        <p>Total Pengeluaran</p>
                        <h2 class="text-danger">Rp 40jt</h2>

                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <div class="stat-card">

                        <div class="stat-icon bg-yellow-soft">
                            <i class="bi bi-exclamation-circle"></i>
                        </div>

                        <p>Total Tunggakan</p>
                        <h2 style="color:#d97706;">Rp 15,7jt</h2>

                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <div class="stat-card">

                        <div class="stat-icon bg-purple-soft">
                            <i class="bi bi-wallet2"></i>
                        </div>

                        <p>Saldo Saat Ini</p>
                        <h2 style="color:#9333ea;">Rp 125jt</h2>

                    </div>
                </div>

            </div>

            <!-- MENU -->
            <h2 class="menu-title">
                Menu Utama
            </h2>

            <div class="row g-4 mb-5">

                <div class="col-lg-3 col-md-6">

                    <div class="menu-card">

                        <div class="menu-icon bg-blue">
                            <i class="bi bi-database"></i>
                        </div>

                        <h4>Data</h4>

                        <p>
                            Kelola data master sistem
                        </p>

                        <a href="#">
                            Buka Menu →
                        </a>

                    </div>

                </div>

                <div class="col-lg-3 col-md-6">

                    <div class="menu-card">

                        <div class="menu-icon bg-green">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>

                        <h4>Pencatatan Penerimaan</h4>

                        <p>
                            Catat pembayaran siswa
                        </p>

                        <a href="#">
                            Buka Menu →
                        </a>

                    </div>

                </div>

                <div class="col-lg-3 col-md-6">

                    <div class="menu-card">

                        <div class="menu-icon bg-red">
                            <i class="bi bi-graph-down-arrow"></i>
                        </div>

                        <h4>Pencatatan Pengeluaran</h4>

                        <p>
                            Catat pengeluaran madrasah
                        </p>

                        <a href="#">
                            Buka Menu →
                        </a>

                    </div>

                </div>

                <div class="col-lg-3 col-md-6">

                    <div class="menu-card">

                        <div class="menu-icon bg-gray">
                            <i class="bi bi-gear"></i>
                        </div>

                        <h4>Pengaturan</h4>

                        <p>
                            Konfigurasi sistem
                        </p>

                        <a href="#">
                            Buka Menu →
                        </a>

                    </div>

                </div>

            </div>

            <!-- =========================
                CHART SECTION
            ========================== -->

            <div class="row g-4">

                <!-- BAR CHART -->
                <div class="col-lg-6">

                    <div class="chart-card">

                        <h3>Pemasukan vs Pengeluaran</h3>

                        <canvas id="barChart"></canvas>

                    </div>

                </div>

                <!-- PIE CHART -->
                <div class="col-lg-6">

                    <div class="chart-card">

                        <h3>Status Pembayaran</h3>

                        <canvas id="pieChart"></canvas>

                        <div class="payment-info">

                            <div class="payment-box">
                                <h4>Lunas</h4>
                                <p class="green">297</p>
                            </div>

                            <div class="payment-box">
                                <h4>Belum Lunas</h4>
                                <p class="orange">45</p>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- =========================
        CHART JS
    ========================== -->

    <script>
    // BAR CHART

    const barCtx = document.getElementById('barChart');

    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei'],
            datasets: [{
                    label: 'Pemasukan',
                    data: [45000000, 52000000, 48000000, 61000000, 55000000],
                    backgroundColor: '#16a34a'
                },
                {
                    label: 'Pengeluaran',
                    data: [35000000, 38000000, 42000000, 45000000, 40000000],
                    backgroundColor: '#ef4444'
                }
            ]
        },
        options: {
            responsive: true
        }
    });

    // PIE CHART

    const pieCtx = document.getElementById('pieChart');

    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['Lunas', 'Belum Lunas'],
            datasets: [{
                data: [297, 45],
                backgroundColor: [
                    '#16a34a',
                    '#f59e0b'
                ]
            }]
        },
        options: {
            responsive: true
        }
    });
    </script>

</body>

</html>