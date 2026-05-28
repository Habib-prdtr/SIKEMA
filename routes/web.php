<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// LOGIN (UI only)
Route::get('/login', function () {
    return view('auth.login');
});

// Dashboard (UI only)
Route::view('/dashboard', 'dashboard.index');

// data tahun ajaran (UI only)
Route::view('/data/tahun-ajaran', 'data.tahun-ajaran.index');

// data siswa (UI only)
Route::view('/data/siswa', 'data.siswa.index');

// jenis penerimaan (UI only)
Route::view('/data/jenis-penerimaan', 'data.jenis-penerimaan.index');

// pos biaya (UI only)
Route::view('/data/pos-biaya', 'data.pos-biaya.index');