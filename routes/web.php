<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Laporan\LaporanPenerimaanController;
use App\Http\Controllers\Laporan\LaporanPengeluaranController;
use App\Http\Controllers\Laporan\RekapitulasiController;
use App\Http\Controllers\Master\DataSiswaController;
use App\Http\Controllers\Master\JenisPenerimaanController;
use App\Http\Controllers\Master\PosBiayaController;
use App\Http\Controllers\Master\SaldoAwalController;
use App\Http\Controllers\Master\SiswaTahunAjaranController;
use App\Http\Controllers\Master\TahunAjaranController;
use App\Http\Controllers\Pengaturan\SekolahController;
use App\Http\Controllers\Transaksi\PenerimaanController;
use App\Http\Controllers\Transaksi\PengeluaranController;
use Illuminate\Support\Facades\Route;

// =========================================================
// AUTH (Publik — tidak perlu login)
// =========================================================

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// =========================================================
// SEMUA ROUTE DI BAWAH DILINDUNGI MIDDLEWARE AUTH
// =========================================================

Route::middleware('auth')->group(function () {

    // ─── Dashboard ────────────────────────────────────────
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ─── Master: Tahun Ajaran ─────────────────────────────
    Route::prefix('master/tahun-ajaran')->name('master.tahun-ajaran.')->group(function () {
        Route::get('/', [TahunAjaranController::class, 'index'])->name('index');
        Route::post('/', [TahunAjaranController::class, 'store'])->name('store');
        Route::patch('/{tahunAjaran}/aktifkan', [TahunAjaranController::class, 'setAktif'])->name('set-aktif');
    });

    // ─── Master: Siswa (Data Induk) ───────────────────────
    Route::prefix('master/siswa')->name('master.siswa.')->group(function () {
        Route::get('/', [DataSiswaController::class, 'index'])->name('index');
        Route::get('/tambah', [DataSiswaController::class, 'create'])->name('create');
        Route::get('/cek-no-induk', [DataSiswaController::class, 'cekNoInduk'])->name('cek-no-induk');
        Route::post('/', [DataSiswaController::class, 'store'])->name('store');
        Route::get('/import', [DataSiswaController::class, 'showImportForm'])->name('import.form');
        Route::post('/import', [DataSiswaController::class, 'import'])->name('import');
        Route::get('/{siswa}/edit', [DataSiswaController::class, 'edit'])->name('edit');
        Route::put('/{siswa}', [DataSiswaController::class, 'update'])->name('update');
        Route::delete('/{siswa}', [DataSiswaController::class, 'destroy'])->name('destroy');
    });

    // ─── Master: Aktivasi Siswa ke Tahun Ajaran ──────────
    Route::prefix('master/siswa-tahun-ajaran')->name('master.siswa-tahun-ajaran.')->group(function () {
        Route::get('/', [SiswaTahunAjaranController::class, 'index'])->name('index');
        Route::post('/', [SiswaTahunAjaranController::class, 'store'])->name('store');
        Route::post('/aktifkan-semua', [SiswaTahunAjaranController::class, 'storeAll'])->name('storeAll');
        Route::put('/{siswaTahunAjaran}', [SiswaTahunAjaranController::class, 'updateSpp'])->name('update');
        Route::patch('/{siswaTahunAjaran}/tunggakan', [SiswaTahunAjaranController::class, 'updateTunggakanAwal'])->name('update.tunggakan');
    });

    // ─── Master: Tarif SPP ────────────────────────────────
    Route::prefix('master/tarif-spp')->name('master.tarif-spp.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Master\MasterTarifSppController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Master\MasterTarifSppController::class, 'store'])->name('store');
        Route::post('/extract', [\App\Http\Controllers\Master\MasterTarifSppController::class, 'extract'])->name('extract');
        Route::put('/{tarifSpp}', [\App\Http\Controllers\Master\MasterTarifSppController::class, 'update'])->name('update');
        Route::delete('/{tarifSpp}', [\App\Http\Controllers\Master\MasterTarifSppController::class, 'destroy'])->name('destroy');
    });

    // ─── Master: Jenis Penerimaan (Iuran) ─────────────────
    Route::prefix('master/jenis-penerimaan')->name('master.jenis-penerimaan.')->group(function () {
        Route::get('/', [JenisPenerimaanController::class, 'index'])->name('index');
        Route::post('/', [JenisPenerimaanController::class, 'store'])->name('store');
        Route::put('/{jenisPenerimaan}', [JenisPenerimaanController::class, 'update'])->name('update');
        Route::delete('/{jenisPenerimaan}', [JenisPenerimaanController::class, 'destroy'])->name('destroy');
        Route::get('/{jenisPenerimaan}/pembayar', [JenisPenerimaanController::class, 'pembayar'])->name('pembayar');
    });

    // ─── Master: Pos Biaya ────────────────────────────────
    Route::prefix('master/pos-biaya')->name('master.pos-biaya.')->group(function () {
        Route::get('/', [PosBiayaController::class, 'index'])->name('index');
        Route::post('/', [PosBiayaController::class, 'store'])->name('store');
        Route::put('/{posBiaya}', [PosBiayaController::class, 'update'])->name('update');
        Route::delete('/{posBiaya}', [PosBiayaController::class, 'destroy'])->name('destroy');
    });

    // ─── Master: Saldo Awal ───────────────────────────────
    Route::prefix('master/saldo-awal')->name('master.saldo-awal.')->group(function () {
        Route::get('/', [SaldoAwalController::class, 'index'])->name('index');
        Route::post('/', [SaldoAwalController::class, 'store'])->name('store');
        Route::put('/{saldoAwal}', [SaldoAwalController::class, 'update'])->name('update');
    });

    // ─── Master: Dispensasi ───────────────────────────────
    Route::prefix('master/dispensasi')->name('master.dispensasi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Master\DispensasiController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Master\DispensasiController::class, 'store'])->name('store');
        Route::put('/{dispensasi}', [\App\Http\Controllers\Master\DispensasiController::class, 'update'])->name('update');
        Route::delete('/{dispensasi}', [\App\Http\Controllers\Master\DispensasiController::class, 'destroy'])->name('destroy');
        Route::get('/{dispensasi}/siswa', [\App\Http\Controllers\Master\DispensasiController::class, 'siswa'])->name('siswa');
        Route::post('/{dispensasi}/siswa', [\App\Http\Controllers\Master\DispensasiController::class, 'tambahSiswa'])->name('siswa.store');
        Route::delete('/{dispensasi}/siswa/{siswaTahunAjaran}', [\App\Http\Controllers\Master\DispensasiController::class, 'hapusSiswa'])->name('siswa.destroy');
    });

    // ─── Transaksi: Penerimaan ────────────────────────────
    Route::prefix('penerimaan')->name('penerimaan.')->group(function () {
        Route::get('/', [PenerimaanController::class, 'index'])->name('index');
        Route::get('/catat', [PenerimaanController::class, 'create'])->name('catat');
        Route::post('/store', [PenerimaanController::class, 'store'])->name('store');
        Route::get('/{transaksi}', [PenerimaanController::class, 'show'])->name('show');
    });

    // ─── Transaksi: Pengeluaran ───────────────────────────
    Route::prefix('pengeluaran')->name('pengeluaran.')->group(function () {
        Route::get('/', [PengeluaranController::class, 'index'])->name('index');
        Route::get('/catat', [PengeluaranController::class, 'create'])->name('catat');
        Route::post('/store', [PengeluaranController::class, 'store'])->name('store');
        Route::get('/{pengeluaran}', [PengeluaranController::class, 'show'])->name('show');
    });

    // ─── Laporan ──────────────────────────────────────────
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/penerimaan', [LaporanPenerimaanController::class, 'index'])->name('penerimaan');
        Route::get('/penerimaan/export', [LaporanPenerimaanController::class, 'export'])->name('penerimaan.export');
        Route::get('/pengeluaran', [LaporanPengeluaranController::class, 'index'])->name('pengeluaran');
        Route::get('/pengeluaran/export', [LaporanPengeluaranController::class, 'export'])->name('pengeluaran.export');
        Route::get('/rekapitulasi', [RekapitulasiController::class, 'index'])->name('rekapitulasi');
        Route::get('/rekapitulasi/export', [RekapitulasiController::class, 'export'])->name('rekapitulasi.export');
    });

    // ─── Pengaturan ───────────────────────────────────────
    Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
        Route::get('/sekolah', [SekolahController::class, 'edit'])->name('sekolah.edit');
        Route::put('/sekolah', [SekolahController::class, 'update'])->name('sekolah.update');
        Route::put('/password', [SekolahController::class, 'updatePassword'])->name('password.update');
    });

});
