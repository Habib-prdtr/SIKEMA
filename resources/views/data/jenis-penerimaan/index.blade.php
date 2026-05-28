@extends('layouts.app')

@section('page-title', 'Jenis Penerimaan')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h1 class="fw-bold">Jenis Penerimaan</h1>

        <p class="text-secondary">
            Kelola jenis dan kategori penerimaan
        </p>
    </div>

    <button class="btn btn-success px-4 py-3 rounded-4 fw-semibold">
        <i class="bi bi-plus-lg"></i>
        Tambah Jenis Penerimaan
    </button>

</div>

<div class="row g-4">

    <!-- KATEGORI BULANAN -->

    <div class="col-lg-6">

        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <h3 class="fw-bold mb-0">
                    Kategori Bulanan (Rutin)
                </h3>

                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                    2 item
                </span>

            </div>

            <!-- ITEM -->

            <div class="border rounded-4 p-4 mb-3">

                <div class="d-flex justify-content-between">

                    <div>
                        <h4 class="fw-bold">
                            SPP
                        </h4>

                        <p class="text-secondary mb-3">
                            Sumbangan Pembinaan Pendidikan bulanan
                        </p>

                        <h3 class="text-success fw-bold">
                            Rp 350.000
                        </h3>
                    </div>

                    <div>
                        <i class="bi bi-pencil-square text-success me-2"></i>
                        <i class="bi bi-trash text-danger"></i>
                    </div>

                </div>

            </div>

            <div class="border rounded-4 p-4">

                <div class="d-flex justify-content-between">

                    <div>
                        <h4 class="fw-bold">
                            Ekstrakulikuler
                        </h4>

                        <p class="text-secondary mb-3">
                            Biaya kegiatan ekstrakurikuler
                        </p>

                        <h3 class="text-success fw-bold">
                            Rp 100.000
                        </h3>
                    </div>

                    <div>
                        <i class="bi bi-pencil-square text-success me-2"></i>
                        <i class="bi bi-trash text-danger"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- KATEGORI SATUAN -->

    <div class="col-lg-6">

        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <h3 class="fw-bold mb-0">
                    Kategori Satuan (Urgent)
                </h3>

                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                    3 item
                </span>

            </div>

            <!-- ITEM -->

            <div class="border rounded-4 p-4 mb-3">

                <div class="d-flex justify-content-between">

                    <div>
                        <h4 class="fw-bold">
                            Uang Gedung
                        </h4>

                        <p class="text-secondary mb-3">
                            Pembayaran satu kali saat masuk
                        </p>

                        <h3 class="text-primary fw-bold">
                            Rp 2.000.000
                        </h3>
                    </div>

                    <div>
                        <i class="bi bi-pencil-square text-success me-2"></i>
                        <i class="bi bi-trash text-danger"></i>
                    </div>

                </div>

            </div>

            <div class="border rounded-4 p-4 mb-3">

                <div class="d-flex justify-content-between">

                    <div>
                        <h4 class="fw-bold">
                            Seragam
                        </h4>

                        <p class="text-secondary mb-3">
                            Seragam sekolah lengkap
                        </p>

                        <h3 class="text-primary fw-bold">
                            Rp 250.000
                        </h3>
                    </div>

                    <div>
                        <i class="bi bi-pencil-square text-success me-2"></i>
                        <i class="bi bi-trash text-danger"></i>
                    </div>

                </div>

            </div>

            <div class="border rounded-4 p-4">

                <div class="d-flex justify-content-between">

                    <div>
                        <h4 class="fw-bold">
                            Buku Paket
                        </h4>

                        <p class="text-secondary mb-3">
                            Paket buku pelajaran
                        </p>

                        <h3 class="text-primary fw-bold">
                            Rp 150.000
                        </h3>
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