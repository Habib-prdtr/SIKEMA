@extends('layouts.app')

@section('page-title', 'Tahun Ajaran')

@section('content')

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 35px;
}

.page-title h1 {
    font-size: 52px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 8px;
}

.page-title p {
    color: #64748b;
    font-size: 20px;
}

.btn-add {
    background: #059669;
    color: white;
    border: none;
    border-radius: 14px;
    padding: 16px 28px;
    font-weight: 600;
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.btn-add:hover {
    background: #047857;
}

.year-card {
    background: white;
    border-radius: 24px;
    padding: 30px;
    border: 1px solid #e5e7eb;
    height: 100%;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.04);
}

.year-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 28px;
}

.year-icon {
    width: 64px;
    height: 64px;
    border-radius: 18px;
    background: #d1fae5;
    color: #059669;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
}

.status-badge {
    padding: 8px 18px;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 600;
}

.badge-active {
    background: #dcfce7;
    color: #15803d;
}

.badge-finished {
    background: #f1f5f9;
    color: #334155;
}

.year-card h3 {
    font-size: 42px;
    font-weight: 700;
    margin-bottom: 24px;
    color: #0f172a;
}

.year-info {
    margin-bottom: 10px;
    font-size: 22px;
    color: #334155;
}

.year-actions {
    display: flex;
    gap: 14px;
    margin-top: 35px;
}

.btn-edit {
    flex: 1;
    border: 1px solid #cbd5e1;
    background: white;
    border-radius: 14px;
    padding: 14px;
    font-size: 24px;
    font-weight: 600;
}

.btn-delete {
    width: 72px;
    border: 1px solid #fecaca;
    color: #ef4444;
    background: white;
    border-radius: 14px;
    font-size: 22px;
}
</style>

<div class="page-header">

    <div class="page-title">
        <h1>Tahun Ajaran</h1>
        <p>Kelola tahun ajaran madrasah</p>
    </div>

    <button class="btn-add">
        <i class="bi bi-plus-lg"></i>
        Tambah Tahun Ajaran
    </button>

</div>

<div class="row g-4">

    <!-- CARD 1 -->
    <div class="col-lg-4">

        <div class="year-card">

            <div class="year-top">

                <div class="year-icon">
                    <i class="bi bi-calendar-event"></i>
                </div>

                <div class="status-badge badge-active">
                    Aktif
                </div>

            </div>

            <h3>2025/2026</h3>

            <div class="year-info">
                Mulai: 15/7/2025
            </div>

            <div class="year-info">
                Selesai: 30/6/2026
            </div>

            <div class="year-actions">

                <button class="btn-edit">
                    <i class="bi bi-pencil-square"></i>
                    Edit
                </button>

                <button class="btn-delete">
                    <i class="bi bi-trash"></i>
                </button>

            </div>

        </div>

    </div>

    <!-- CARD 2 -->
    <div class="col-lg-4">

        <div class="year-card">

            <div class="year-top">

                <div class="year-icon">
                    <i class="bi bi-calendar-event"></i>
                </div>

                <div class="status-badge badge-finished">
                    Selesai
                </div>

            </div>

            <h3>2024/2025</h3>

            <div class="year-info">
                Mulai: 15/7/2024
            </div>

            <div class="year-info">
                Selesai: 30/6/2025
            </div>

            <div class="year-actions">

                <button class="btn-edit">
                    <i class="bi bi-pencil-square"></i>
                    Edit
                </button>

                <button class="btn-delete">
                    <i class="bi bi-trash"></i>
                </button>

            </div>

        </div>

    </div>

    <!-- CARD 3 -->
    <div class="col-lg-4">

        <div class="year-card">

            <div class="year-top">

                <div class="year-icon">
                    <i class="bi bi-calendar-event"></i>
                </div>

                <div class="status-badge badge-finished">
                    Selesai
                </div>

            </div>

            <h3>2023/2024</h3>

            <div class="year-info">
                Mulai: 15/7/2023
            </div>

            <div class="year-info">
                Selesai: 30/6/2024
            </div>

            <div class="year-actions">

                <button class="btn-edit">
                    <i class="bi bi-pencil-square"></i>
                    Edit
                </button>

                <button class="btn-delete">
                    <i class="bi bi-trash"></i>
                </button>

            </div>

        </div>

    </div>

</div>

@endsection