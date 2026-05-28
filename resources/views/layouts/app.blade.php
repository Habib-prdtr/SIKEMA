<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SIKEMA Dashboard</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
    * {
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: #f5f7fb;
        overflow-x: hidden;
    }

    /* ======================
            SIDEBAR
        ====================== */

    .sidebar {
        width: 300px;
        height: 100vh;
        background: white;
        border-right: 1px solid #e5e7eb;
        position: fixed;
        left: 0;
        top: 0;
        overflow-y: auto;
    }

    .sidebar-header {
        padding: 28px;
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
        box-shadow: 0 10px 20px rgba(16, 185, 129, 0.25);
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
        padding: 20px 15px;
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
        margin-left: 45px;
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

    .submenu a.active {
        color: #059669;
        font-weight: 600;
    }

    /* ======================
            MAIN
        ====================== */

    .main-content {
        margin-left: 300px;
        min-height: 100vh;
    }

    /* ======================
            TOPBAR
        ====================== */

    .topbar {
        height: 80px;
        background: white;
        border-bottom: 1px solid #e5e7eb;
        padding: 0 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .breadcrumb-text {
        color: #64748b;
        font-size: 15px;
    }

    .breadcrumb-text span {
        color: #0f172a;
        font-weight: 600;
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

    /* ======================
            CONTENT
        ====================== */

    .content {
        padding: 40px;
    }

    /* ======================
            RESPONSIVE
        ====================== */

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
            height: auto;
        }

        .content {
            padding: 20px;
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

            <!-- DASHBOARD -->
            <a href="/dashboard" class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">

                <div class="menu-left">
                    <i class="bi bi-house-door"></i>
                    <span>Dashboard</span>
                </div>

            </a>

            <!-- DATA -->
            <a href="#" class="menu-item {{ request()->is('data/*') ? 'active' : '' }}">

                <div class="menu-left">
                    <i class="bi bi-database"></i>
                    <span>Data</span>
                </div>

                <i class="bi bi-chevron-right"></i>

            </a>

            <!-- SUBMENU -->
            <div class="submenu">

                <a href="/data/tahun-ajaran" class="{{ request()->is('data/tahun-ajaran') ? 'active' : '' }}">
                    Tahun Ajaran
                </a>

                <a href="/data/siswa" class="{{ request()->is('data/siswa') ? 'active' : '' }}">
                    Data Siswa
                </a>

                <a href="/data/siswa-per-tahun-ajaran"
                    class="{{ request()->is('data/siswa-per-tahun-ajaran') ? 'active' : '' }}">
                    Siswa per Tahun Ajaran
                </a>

                <a href="/data/jenis-penerimaan" class="{{ request()->is('data/jenis-penerimaan') ? 'active' : '' }}">
                    Jenis Penerimaan
                </a>

                <a href="/data/pos-biaya" class="{{ request()->is('data/pos-biaya') ? 'active' : '' }}">
                    Pos Biaya
                </a>

                <a href="#">
                    Rekap Pembayaran
                </a>

            </div>

            <!-- PENERIMAAN -->
            <a href="#" class="menu-item">

                <div class="menu-left">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Penerimaan</span>
                </div>

                <i class="bi bi-chevron-right"></i>

            </a>

            <!-- PENGELUARAN -->
            <a href="#" class="menu-item">

                <div class="menu-left">
                    <i class="bi bi-graph-down-arrow"></i>
                    <span>Pengeluaran</span>
                </div>

                <i class="bi bi-chevron-right"></i>

            </a>

            <!-- PENGATURAN -->
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

            <div class="breadcrumb-text">
                Data
                <i class="bi bi-chevron-right mx-2"></i>
                <span>@yield('page-title')</span>
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
            @yield('content')
        </div>

    </div>

</body>

</html>