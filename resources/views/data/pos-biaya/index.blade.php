@extends('layouts.app')

@section('page-title', 'Pos Biaya')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h1 class="fw-bold">Pos Biaya</h1>

        <p class="text-secondary">
            Kelola kategori pos biaya pengeluaran
        </p>
    </div>

    <button class="btn btn-success px-4 py-3 rounded-4 fw-semibold">
        <i class="bi bi-plus-lg"></i>
        Tambah Pos Biaya
    </button>

</div>

<div class="row g-4">

    <!-- OPERASIONAL -->

    <div class="col-lg-4">

        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <h3 class="fw-bold">
                    Operasional
                </h3>

                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                    5
                </span>

            </div>

            <!-- ITEM -->

            <div class="border border-success rounded-4 p-4 mb-3">

                <div class="d-flex justify-content-between">

                    <div>
                        <h4 class="fw-bold">
                            Gaji Guru
                        </h4>

                        <p class="text-secondary">
                            Gaji bulanan tenaga pendidik
                        </p>
                    </div>

                    <div>
                        <i class="bi bi-pencil-square text-success me-2"></i>
                        <i class="bi bi-trash text-danger"></i>
                    </div>

                </div>

            </div>

            <div class="border border-success rounded-4 p-4 mb-3">

                <div class="d-flex justify-content-between">

                    <div>
                        <h4 class="fw-bold">
                            Gaji Staff
                        </h4>

                        <p class="text-secondary">
                            Gaji bulanan staff administrasi
                        </p>
                    </div>

                    <div>
                        <i class="bi bi-pencil-square text-success me-2"></i>
                        <i class="bi bi-trash text-danger"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- UTILITAS -->

    <div class="col-lg-4">

        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <h3 class="fw-bold">
                    Utilitas
                </h3>

                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                    2
                </span>

            </div>

            <div class="border border-primary rounded-4 p-4 mb-3">

                <div class="d-flex justify-content-between">

                    <div>
                        <h4 class="fw-bold">
                            Listrik & Air
                        </h4>

                        <p class="text-secondary">
                            Biaya listrik dan air bulanan
                        </p>
                    </div>

                    <div>
                        <i class="bi bi-pencil-square text-success me-2"></i>
                        <i class="bi bi-trash text-danger"></i>
                    </div>

                </div>

            </div>

            <div class="border border-primary rounded-4 p-4">

                <div class="d-flex justify-content-between">

                    <div>
                        <h4 class="fw-bold">
                            Internet & Telepon
                        </h4>

                        <p class="text-secondary">
                            Biaya komunikasi
                        </p>
                    </div>

                    <div>
                        <i class="bi bi-pencil-square text-success me-2"></i>
                        <i class="bi bi-trash text-danger"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- MAINTENANCE -->

    <div class="col-lg-4">

        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <h3 class="fw-bold">
                    Maintenance
                </h3>

                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2">
                    1
                </span>

            </div>

            <div class="border border-warning rounded-4 p-4">

                <div class="d-flex justify-content-between">

                    <div>
                        <h4 class="fw-bold">
                            Pemeliharaan Gedung
                        </h4>

                        <p class="text-secondary">
                            Perawatan dan perbaikan gedung
                        </p>
                    </div>

                    <div>
                        <i class="bi bi-pencil-square text-success me-2"></i>
                        <i class="bi bi-trash text-danger"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection