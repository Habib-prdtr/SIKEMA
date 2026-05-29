@echo off
rtk git add .
rtk git commit -m "fix: perbaikan ParseError dan form di halaman Jenis Penerimaan" -m "- Perbaiki ParseError di Blade dengan mengganti @json menjadi json_encode() pada form edit" -m "- Seragamkan variabel 'nominal' menjadi 'tarif' di form dan controller agar sesuai dengan database" -m "- Tambahkan validasi 'urutan' dan 'keterangan' pada Update request agar data tersimpan saat diedit" -m "- Tambahkan validasi 'keterangan' pada Store request"
rtk git push origin habib-dev
