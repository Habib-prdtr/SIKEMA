# Quality Control (QC) Checklist
Fokus dari QC di proyek SIKEMA adalah melakukan inspeksi dan verifikasi *artefak/produk* SEBELUM masuk ke tahap pengujian (Testing). Tugas QC memastikan semuanya sudah "siap uji" dan sesuai spesifikasi.

Berikan centang `[x]` jika verifikasi produk lulus.

## 📋 1. Verifikasi Requirement (Kesesuaian Spesifikasi)
- [ ] Fitur yang diserahkan programmer sudah 100% sesuai dengan permintaan klien (tidak ada fitur wajib yang kurang).
- [ ] Alur bisnis keuangan (Penerimaan & Pengeluaran) sudah memenuhi standar pembukuan Madrasah.

## 🎨 2. Verifikasi UI/UX (Desain & Tampilan)
- [ ] Tampilan antarmuka (*User Interface*) sudah sesuai dengan rancangan/desain (Tailwind).
- [ ] Halaman web responsif dan tidak berantakan jika dibuka melalui HP/Tablet.
- [ ] Kosakata pada tabel, tombol, dan form sudah menggunakan bahasa Indonesia yang baku dan konsisten.

## ⚙️ 3. Inspeksi Kepatuhan Kode (Code QC)
- [ ] Programmer telah memenuhi semua syarat di file `QA_STANDARDS.md`.
- [ ] Telah dicek ulang bahwa tidak ada kode *debugging* (seperti `dd()`, `var_dump`, atau `console.log`) yang tertinggal dan masuk ke *production*.
- [ ] Tidak ada password / kredensial sensitif yang sengaja ditulis mentah-mentah (*hardcode*) di dalam kode.

## 📦 4. Verifikasi Environment & Deployment
- [ ] Jika ada penambahan library baru, file `composer.json` atau `package.json` sudah di-update.
- [ ] Jika ada perubahan variabel lingkungan, file `.env.example` sudah disesuaikan.
- [ ] Proses *migrasi* database berjalan bersih tanpa error (`php artisan migrate:fresh`).
