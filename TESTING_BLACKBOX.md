# Blackbox Testing Checklist - SIKEMA
Dokumen ini khusus digunakan oleh tim **Tester** untuk menguji fungsionalitas aplikasi dari sudut pandang *User* akhir (tanpa melihat/peduli dengan kode).

Tugas Tester adalah mencoba memasukkan input yang normal hingga input yang tidak wajar untuk melihat ketahanan sistem.

## 🔐 1. Modul Otentikasi & Akses
- [ ] **Gagal Login:** Login dengan password salah (Ekspektasi: Muncul error, gagal masuk).
- [ ] **Bypass URL:** Langsung mengetikkan URL `/laporan` tanpa login (Ekspektasi: Ditendang kembali ke form login).

## 👥 2. Modul Master Data
- [ ] **Duplikasi Data:** Input Siswa dengan NISN yang sudah terdaftar (Ekspektasi: Ditolak dengan pesan error validasi).
- [ ] **Hapus Data Aktif:** Menghapus data Pos Biaya/Jenis Penerimaan yang riwayat transaksinya sudah ada (Ekspektasi: Dilarang dihapus, muncul *warning*).

## 💰 3. Modul Transaksi (Penerimaan & Pengeluaran)
- [ ] **Input Minus:** Memasukkan nominal transaksi dengan angka `-50000` (Ekspektasi: Ditolak).
- [ ] **Input Huruf:** Memasukkan huruf di kolom angka nominal (Ekspektasi: Ditolak).
- [ ] **Spam Click:** Menekan tombol "Simpan Transaksi" 5 kali berturut-turut dengan cepat (Ekspektasi: Hanya 1 transaksi yang tersimpan, tidak ter-*duplicate*).
- [ ] **Verifikasi Saldo Akhir:** Memastikan penambahan Kas Masuk dan Kas Keluar secara otomatis mengkalkulasi saldo akhir di *Dashboard* dengan akurat.

## 📊 4. Modul Laporan
- [ ] **Laporan Kosong:** Memfilter tanggal laporan pada bulan yang tidak ada transaksi (Ekspektasi: Tabel kosong bertuliskan "Data tidak ditemukan", tidak boleh *error* 500).
- [ ] **Akurasi Penjumlahan:** Menjumlahkan deretan angka laporan menggunakan kalkulator, lalu bandingkan dengan baris "Total" pada web (Ekspektasi: Sama persis).
