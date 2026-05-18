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