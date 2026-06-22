# SIKEMA

Sistem Informasi Keuangan Madrasah.

## Teknologi

- Laravel
- Vite

## Instalasi

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## Menjalankan Aplikasi

### Cara 1: Menggunakan Desktop Launcher (Sekali Klik)
Jika Anda menggunakan Windows dan ingin menjalankan aplikasi ini dengan sekali klik:
1. Jalankan/klik ganda file `jalankan_sikema.bat` yang ada di root folder ini.
2. Jendela Command Prompt akan muncul dan secara otomatis membuka browser Anda ke alamat `http://localhost:8000`.
3. **Penting**: Jangan tutup jendela Command Prompt tersebut selama Anda menggunakan aplikasi. Jika sudah selesai, Anda cukup menutup jendela Command Prompt tersebut untuk mematikan server aplikasi.

*Tips: Anda dapat membuat shortcut dari file `jalankan_sikema.bat` ke Desktop Anda dan mengubah ikonnya agar terlihat seperti aplikasi biasa.*

### Cara 2: Manual (Mode Pengembangan)
```bash
php artisan serve
npm run dev
```
