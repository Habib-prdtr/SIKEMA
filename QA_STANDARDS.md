# Quality Assurance (QA) & Tech Standards SIKEMA
Dokumen ini adalah acuan standar teknologi, struktur, dan penulisan kode untuk proyek SIKEMA. QA berhak **menolak kode (Reject PR)** jika tidak mencentang/memenuhi standar di bawah ini.

## 🛠️ 1. Standar Teknologi (Tech Stack)
Pastikan lingkungan pengembangan Anda menggunakan versi yang disepakati:
- [x] **PHP:** Versi 8.3 atau lebih baru.
- [x] **Framework:** Laravel (Sesuai `composer.json`).
- [x] **Frontend:** Node.js, Vite, TailwindCSS v4.
- [x] **Format Kode:** Menggunakan `laravel/pint` (jalankan `./vendor/bin/pint` sebelum commit).

## 📂 2. Standar Struktur & Penamaan (Naming Conventions)
- [x] **Database (Tabel & Kolom):** Wajib menggunakan *snake_case* dan jamak (contoh: `kas_masuk`, `users`, `created_at`).
- [x] **Model:** Wajib menggunakan *PascalCase* dan tunggal (contoh: `KasMasuk`, `User`).
- [x] **Controller:** Wajib menggunakan *PascalCase* dengan akhiran Controller (contoh: `KasMasukController`).
- [x] **Views (Blade):** Wajib menggunakan *kebab-case* atau *snake_case* (contoh: `kas-masuk-index.blade.php`).
- [x] **Routes:** URL menggunakan *kebab-case* (contoh: `/kas-masuk`), sedangkan penamaan rute menggunakan dot (contoh: `->name('kas.masuk')`).

## 💻 3. Standar Penulisan Kode (Coding Standards)
Saat membuat fitur baru, programmer wajib mematuhi aturan ini:
- [x] **Validasi Terpisah:** DILARANG melakukan validasi langsung di Controller (`$request->validate()`). **Wajib** membuat *Form Request* (`php artisan make:request`).
- [x] **Fat Model, Skinny Controller:** Logika Query DB yang rumit (banyak join/where) harus dipindah ke Model (sebagai *Local Scope*) atau *Service Class*.
- [x] **Integritas Transaksi:** Fitur Insert/Update/Delete **wajib** menggunakan `DB::beginTransaction()`, `DB::commit()`, dibungkus dalam `try-catch`, dan diakhiri `DB::rollBack()` jika *error*.
- [x] **Anti N+1 Query:** Saat memanggil data yang berelasi di *looping* Blade, **wajib** menggunakan Eager Loading di Controller (menggunakan `with()`).
- [x] **Tidak Ada String Hardcoded (Magic Numbers):** Status atau tipe (seperti 'aktif', 'pending') harus dijadikan *Constants* atau *Enum* di dalam Model.

## 🧰 4. Standar Penggunaan Helper & Keamanan
- [x] **Custom Helper (Keuangan & Tampilan):** Pemformatan uang (Rupiah) atau tanggal dilarang diketik manual di Blade. **Wajib** membuat Custom Helper (misal: `format_rupiah($nominal)`) atau *Blade Component*.
- [x] **Penanganan Pesan Error (Error Codes):** Pesan balasan error (seperti HTTP 404, 500, atau validasi gagal) tidak boleh di-*hardcode* teksnya satu per satu di Controller. **Wajib** menggunakan Helper terpusat untuk format pesan error (contoh: `api_error_response($code, $message)`).
- [x] **Enkripsi ID pada Route (Keamanan):** DILARANG keras mengekspos ID asli database ke publik di dalam URL (contoh salah: `/kas/edit/1`). Agar konsisten di seluruh aplikasi, **Wajib menggunakan metode Hashids** untuk menyandikan ID tersebut sehingga URL menjadi aman (contoh benar: `/kas/edit/xJ9a2P`).

## 🔄 5. Alur Kerja QA (Agile Scrum)
Sebagai bagian dari metode Agile Scrum, programmer dan QA wajib mematuhi alur kerja berikut:
- [ ] **Shift-Left Analysis:** QA berhak membedah dan mereviu celah logika dari suatu fitur **sebelum** programmer mulai mengetik kode (saat *Sprint Planning*).
- [ ] **Iterasi Standar:** Aturan di dalam file `QA_STANDARDS.md` ini tidak baku dan dapat ditambah/diperbarui kapan saja oleh QA jika ditemukan celah *bug* baru di lapangan pada *Sprint* berjalan.
- [ ] **Pengecekan Otomatis (CI/CD / Hooks):** Programmer tidak boleh mem-*bypass* aturan kode. Ke depannya, pengecekan *style* (`laravel/pint`) dan *error* statis akan dicegat secara otomatis oleh sistem sebelum kode bisa masuk ke *branch* utama.

## 🏃 7. Pembagian Tugas & Sprint Backlog (Agile Scrum)
Aplikasi SIKEMA dikerjakan secara bertahap dalam beberapa *Sprint*. Berikut adalah pembagian tanggung jawab (*PIC*) untuk dua programmer saat ini (**Habib** dan **Luthfi**):

**Sprint 1 (Fondasi & Master Data)**
- [ ] **Modul Pengaturan & Auth (Sekolah, Dashboard Awal):** 👨‍💻 Habib
- [ ] **Modul Master (Data Siswa, Tahun Ajaran, Pos Biaya):** 👨‍💻 Luthfi

**Sprint 2 (Inti Transaksi Keuangan)**
- [ ] **Transaksi Penerimaan (Tagihan, SPP, Iuran):** 👨‍💻 Habib
- [ ] **Transaksi Pengeluaran (Pembayaran Kas):** 👨‍💻 Luthfi

**Sprint 3 (Pelaporan & Finalisasi)**
- [ ] **Laporan Penerimaan & Cetak Bukti:** 👨‍💻 Habib
- [ ] **Laporan Pengeluaran & Rekap Saldo Akhir:** 👨‍💻 Luthfi

*(Setiap kali sebuah fitur selesai di setiap Sprint, programmer wajib membuat Pull Request untuk direviu dan di-QC terlebih dahulu)*

## 🌿 6. Standar Version Control (Git & Branching)
Untuk mencegah bentrok kode (*conflict*) dan menjaga riwayat tetap bersih, seluruh tim **wajib** mengikuti aturan Git berikut:
- [ ] **Pemisahan Branch:** DILARANG KERAS mem-*push* kode langsung ke branch `main`. Branch `main` adalah area suci yang hanya berisi kode yang sudah lolos uji QA.
- [ ] **Penamaan Branch Pribadi:** Setiap programmer harus membuat dan bekerja di *branch* masing-masing dengan format `nama_dev` atau `nama_dev/nama-fitur` (contoh: `budi` atau `budi/fitur-kas-masuk`).
- [ ] **Standar Pesan Commit:** Pesan *commit* tidak boleh asal-asalan (seperti `"update"` atau `"fix error"`). **Wajib** menggunakan format yang jelas (contoh: `feat: menambahkan tombol cetak PDF` atau `fix: memperbaiki pesan error di form siswa`).
- [ ] **Penggabungan Kode (Pull Request):** Kode dari *branch* programmer hanya boleh masuk ke `main` melalui proses *Pull Request (PR)* dan wajib mendapat persetujuan (di-*Approve*) oleh QA terlebih dahulu.

---
**Pernyataan Programmer:**
*(Saat programmer menyerahkan tugasnya melalui Pull Request ke QA, mereka harus memastikan poin-poin di atas sudah terceklis)*
