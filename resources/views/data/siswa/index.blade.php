@extends('layouts.app')

@section('page-title', 'Data Siswa')

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

.table-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #e5e7eb;
    padding: 24px;
}

.table-tools {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    gap: 20px;
}

.search-box {
    flex: 1;
    position: relative;
}

.search-box input {
    width: 100%;
    height: 58px;
    border-radius: 14px;
    border: 1px solid #cbd5e1;
    padding-left: 50px;
    font-size: 17px;
}

.search-box i {
    position: absolute;
    top: 50%;
    left: 18px;
    transform: translateY(-50%);
    color: #64748b;
}

.filter-group {
    display: flex;
    gap: 16px;
}

.filter-group select,
.filter-group button {
    height: 58px;
    border-radius: 14px;
    border: 1px solid #cbd5e1;
    background: white;
    padding: 0 20px;
    font-weight: 500;
}

table {
    width: 100%;
}

thead {
    background: #f8fafc;
}

th {
    padding: 18px;
    color: #64748b;
    font-size: 14px;
}

td {
    padding: 22px 18px;
    border-top: 1px solid #e2e8f0;
    font-size: 16px;
    color: #0f172a;
}

.status {
    background: #dcfce7;
    color: #15803d;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 600;
}

.action-btns {
    display: flex;
    gap: 14px;
}

.edit-btn {
    color: #059669;
    font-size: 20px;
}

.delete-btn {
    color: #ef4444;
    font-size: 20px;
}

.table-footer {
    margin-top: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pagination-custom {
    display: flex;
    gap: 10px;
}

.pagination-custom button {
    border: 1px solid #cbd5e1;
    background: white;
    border-radius: 10px;
    padding: 10px 16px;
}

.pagination-custom .active {
    background: #059669;
    color: white;
    border: none;
}
</style>

<div class="page-header">

    <div class="page-title">
        <h1>Data Siswa</h1>
        <p>Kelola data siswa madrasah</p>
    </div>

    <button class="btn-add">
        <i class="bi bi-plus-lg"></i>
        Tambah Siswa
    </button>

</div>

<div class="table-card">

    <div class="table-tools">

        <div class="search-box">

            <i class="bi bi-search"></i>

            <input type="text" placeholder="Cari nama, no induk, atau NISN...">

        </div>

        <div class="filter-group">

            <select>
                <option>Semua Kelas</option>
            </select>

            <button>
                <i class="bi bi-download"></i>
                Export
            </button>

        </div>

    </div>

    <table>

        <thead>

            <tr>
                <th>NO INDUK</th>
                <th>NAMA SISWA</th>
                <th>NISN</th>
                <th>KELAS</th>
                <th>STATUS</th>
                <th>AKSI</th>
            </tr>

        </thead>

        <tbody>

            <tr>
                <td>2024001</td>
                <td>Ahmad Fauzi</td>
                <td>0012345678</td>
                <td>7A</td>
                <td><span class="status">Aktif</span></td>
                <td>
                    <div class="action-btns">
                        <i class="bi bi-pencil-square edit-btn"></i>
                        <i class="bi bi-trash delete-btn"></i>
                    </div>
                </td>
            </tr>

            <tr>
                <td>2024002</td>
                <td>Siti Nurhaliza</td>
                <td>0012345679</td>
                <td>8B</td>
                <td><span class="status">Aktif</span></td>
                <td>
                    <div class="action-btns">
                        <i class="bi bi-pencil-square edit-btn"></i>
                        <i class="bi bi-trash delete-btn"></i>
                    </div>
                </td>
            </tr>

            <tr>
                <td>2024003</td>
                <td>Muhammad Rizki</td>
                <td>0012345680</td>
                <td>9A</td>
                <td><span class="status">Aktif</span></td>
                <td>
                    <div class="action-btns">
                        <i class="bi bi-pencil-square edit-btn"></i>
                        <i class="bi bi-trash delete-btn"></i>
                    </div>
                </td>
            </tr>

        </tbody>

    </table>

    <div class="table-footer">

        <div>
            Menampilkan 3 dari 8 siswa
        </div>

        <div class="pagination-custom">

            <button>Previous</button>

            <button class="active">1</button>

            <button>2</button>

            <button>Next</button>

        </div>

    </div>

</div>

@endsection