@echo off
echo Menjalankan database seeder...
"C:\laragon\bin\php\php-8.2.29-nts-Win32-vs16-x64\php.exe" artisan db:seed --class=DummyDataSeeder
echo.
echo Selesai! Exit code: %ERRORLEVEL%
pause
