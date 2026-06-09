# Quality Control (QC) Checklist - SIKEMA

Fokus dari QC di proyek SIKEMA adalah melakukan inspeksi dan verifikasi *artefak/produk* SEBELUM masuk ke tahap pengujian (Testing). Tugas QC memastikan semuanya sudah "siap uji" dan sesuai spesifikasi, dengan memverifikasi secara langsung pemenuhan standar yang ditetapkan dalam `QA_STANDARDS.md`.

## 📋 1. Verifikasi Requirement (Kesesuaian Spesifikasi)
- [x] **Kesesuaian Fitur:** Fitur yang diserahkan programmer sudah 100% sesuai dengan permintaan klien (tidak ada fitur wajib yang kurang). *(✅ Semua modul utama dari Sprint 1-3 mulai dari Auth, Master Data, Transaksi Penerimaan/Pengeluaran, Laporan, hingga Pengaturan Sekolah sudah terimplementasi)*
- [x] **Alur Pembukuan:** Alur bisnis keuangan (Penerimaan & Pengeluaran) sudah memenuhi standar pembukuan Madrasah. *(✅ Pembagian pos biaya, jenis penerimaan iuran/SPP bulanan, dan pengeluaran kas tercatat secara teratur)*

## 🎨 2. Verifikasi UI/UX (Desain & Tampilan)
- [x] **Kesesuaian Desain:** Tampilan antarmuka (*User Interface*) sudah sesuai dengan rancangan/desain (Tailwind). *(✅ Menggunakan TailwindCSS v4 dengan tema warna dan komponen antarmuka yang rapi)*
- [x] **Responsivitas:** Halaman web responsif dan tidak berantakan jika dibuka melalui HP/Tablet. *(✅ Layout fleksibel, navigasi adaptif untuk perangkat mobile)*
- [x] **Konsistensi Bahasa:** Kosakata pada tabel, tombol, dan form sudah menggunakan bahasa Indonesia yang baku. *(✅ Menggunakan istilah standar: "Tahun Ajaran", "Pos Biaya", "Siswa", "Transaksi Penerimaan/Pengeluaran", dsb.)*

## 🛠️ 3. Inspeksi Standar Teknologi (Tech Stack QC)
- [x] **PHP Version:** Memastikan PHP yang digunakan versi 8.3 atau lebih baru. *(✅ `composer.json` mensyaratkan `^8.3`)*
- [x] **Laravel Framework:** Memastikan menggunakan Laravel Framework sesuai `composer.json`. *(✅ Laravel `^13.7` terpasang)*
- [x] **Frontend Tools:** Memastikan menggunakan Node.js, Vite, dan TailwindCSS v4. *(✅ `package.json` menggunakan `tailwindcss ^4.0.0` & `vite ^8.0.0`)*
- [x] **Code Formatter:** Memastikan menggunakan `laravel/pint` untuk formatting sebelum commit. *(✅ `laravel/pint ^1.27` terpasang di `require-dev`)*

## 📂 4. Inspeksi Struktur & Penamaan (Naming Conventions QC)
- [x] **Database Tables & Columns:** Nama tabel dan kolom menggunakan *snake_case* dan jamak. *(✅ Terbukti di migration: `tagihan_spp`, `tahun_ajaran`, `siswa_tahun_ajaran`)*
- [x] **Models:** Nama Model menggunakan *PascalCase* dan tunggal. *(✅ Contoh: `TagihanSpp`, `SiswaTahunAjaran`, `PosBiaya`)*
- [x] **Controllers:** Nama Controller menggunakan *PascalCase* dengan akhiran Controller. *(✅ Contoh: `PenerimaanController`, `DataSiswaController`, `PosBiayaController`)*
- [x] **Views (Blade):** Nama file view menggunakan *kebab-case* atau *snake_case*. *(✅ Contoh folder: `jenis-penerimaan/`, `siswa-tahun-ajaran/`, `pos-biaya/`)*
- [x] **Routes:** URL menggunakan *kebab-case* dan penamaan rute menggunakan dot. *(✅ Contoh: `/master/tarif-spp` → `name('master.tarif-spp.index')`)*

## 💻 5. Inspeksi Kepatuhan Penulisan Kode (Coding Standards QC)
- [x] **Validasi Terpisah:** Validasi dipisah ke Form Request dan dilarang langsung di Controller. *(✅ Berhasil dipisah menggunakan `StoreTarifSppRequest`, `UpdateTarifSppRequest`, dan `UpdateSiswaTahunAjaranSppRequest`)*
- [x] **Fat Model, Skinny Controller:** Logika Query rumit dipindah ke Model atau Service Class. *(✅ Ada `app/Services/`: `TransaksiService`, `MasterDataService`, `LaporanService`, `TagihanService`, dll.)*
- [x] **Integritas Transaksi:** Fitur Insert/Update/Delete menggunakan DB transaksi atomik secara aman. *(✅ Diterapkan di `SiswaTahunAjaranController`, `JenisPenerimaanController`, dan `DB::transaction()` di `TransaksiService`)*
- [x] **Anti N+1 Query:** Menggunakan Eager Loading `with()` saat meload relasi. *(✅ `with()` digunakan secara konsisten di semua Controller dan Service)*
- [x] **Anti Hardcoded Strings:** Menggunakan Constants atau Enum di Model untuk status/tipe. *(✅ Contoh: `TagihanSpp::STATUS_BELUM`, `STATUS_CICILAN`, `STATUS_LUNAS`; `TagihanIuran` & `Siswa` juga punya constants)*

## 🧰 6. Inspeksi Helper & Keamanan (Helper & Security QC)
- [x] **Custom Helpers:** Pemformatan uang/tanggal menggunakan helper global. *(✅ `app/Helpers/SiKemaHelper.php` memuat `format_rupiah()` & `format_tanggal()`, di-autoload lewat `composer.json`)*
- [x] **Error Handling:** Pesan error menggunakan helper terpusat. *(✅ `SiKemaHelper.php` memuat `api_error_response()` & `api_success_response()`)*
- [x] **ID Route Encryption:** ID database disamarkan di URL menggunakan Hashids. *(✅ Package `vinkla/hashids ^14.0` terpasang & `app/Traits/HasHashids.php` diterapkan di Model)*
- [x] **Bebas Kode Debugging:** Tidak ada kode debugging (`dd()`, `var_dump()`, `console.log()`) di production. *(✅ Bersih berdasarkan hasil audit static grep-search di semua folder)*
- [x] **Keamanan Kredensial:** Tidak ada password / kredensial sensitif hardcoded di dalam kode. *(✅ Semua kredensial sistem diamankan di dalam `.env`)*

## 📦 7. Verifikasi Environment & Version Control (DevOps QC)
- [x] **Library Dependencies:** `composer.json` & `package.json` ter-update sesuai library yang digunakan. *(✅ Dependency `vinkla/hashids` dan `docx` terdokumentasi dengan baik)*
- [x] **Environment Variables:** `.env.example` sinkron dengan perubahan variabel lingkungan. *(✅ Konfigurasi database diselaraskan)*
- [x] **Migrasi Database:** Migrasi database berjalan bersih tanpa error. *(✅ Migrasi berjalan lancar)*
- [x] **Pemisahan Branch:** Tidak ada push langsung ke main. *(✅ Ada branch `habib-dev`, `ichsan`, `dev/ubay`, `feature/login-page`)*
- [x] **Penamaan Branch:** Branch dev menggunakan format `nama_dev` atau `nama_dev/fitur`. *(✅ Contoh: `habib-dev`, `dev/ubay`, `ichsan`)*
- [x] **Pesan Commit:** Commit message menggunakan prefix standar (`feat`, `fix`, `refactor`, `chore`). *(✅ Git log menunjukkan penggunaan prefix secara konsisten)*
- [x] **Penggabungan Pull Request:** Merge ke main wajib melalui proses PR dan disetujui QA. *(✅ Merge ke main tercatat melalui Pull Request)*

## 🔄 8. Verifikasi Revisi Klien (Client Revision QC)
- [x] **Master Data - Populate Tahun Ajaran (2025-2050):** Pengisian otomatis / seeder tahun ajaran dari tahun 2025 sampai 2050. *(✅ Sudah diterapkan via `MasterDataSeeder`)*
- [x] **Master Data - Import Siswa via Excel:** Fitur unggah berkas Excel untuk mengimpor data siswa secara massal. *(✅ Sudah diterapkan via route/controller `DataSiswaController@import`)*
- [x] **Penerimaan - Shortcut Pencarian & Pembayaran Cepat di Dashboard:** Pencarian siswa di dashboard yang dapat diklik untuk memilih menu pembayaran secara langsung. *(✅ Sudah diterapkan via fitur "Pencatatan Penerimaan Cepat" di Dashboard)*

