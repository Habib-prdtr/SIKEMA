@echo off
title SIKEMA Desktop Launcher
echo ===================================================
echo             SIKEMA DESKTOP LAUNCHER
echo ===================================================
echo.

:: Memeriksa apakah PHP terinstal
where php >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] PHP tidak ditemukan di sistem Anda!
    echo Silakan install PHP terlebih dahulu dan pastikan sudah masuk ke PATH Environment Variables.
    echo.
    echo Hubungi pengembang atau instal PHP versi 8.3 ke atas.
    echo Tekan tombol apa saja untuk keluar...
    pause >nul
    exit /b
)

echo Menyiapkan server lokal...

:: Jalankan browser secara otomatis setelah jeda 2 detik di background
start "" cmd /c "timeout /t 2 /nobreak >nul && start http://localhost:8000"

:: Jalankan Laravel Development Server
echo Server berjalan di http://localhost:8000
echo.
echo Petunjuk:
echo - JANGAN tutup jendela cmd ini selama menggunakan SIKEMA.
echo - Tutup jendela cmd ini jika sudah selesai menggunakan SIKEMA.
echo.
echo ===================================================
echo LOG SERVER:
php artisan serve --port=8000
