<x-layouts.app title="Import Siswa">
    <x-slot:pageTitle>Master Data / Siswa / Import</x-slot:pageTitle>

    <div class="max-w-2xl space-y-5">

        <div class="flex items-center gap-4">
            <a href="{{ route('master.siswa.index') }}" class="btn-secondary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Import Siswa dari Excel</h1>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">Unggah Berkas Excel</h3>
            </div>
            <form method="POST" action="{{ route('master.siswa.import') }}" enctype="multipart/form-data" class="card-body space-y-6">
                @csrf

                <div class="space-y-2">
                    <label for="file" class="form-label font-medium">Pilih Berkas Excel (.xlsx, .xls, .xlsm, .csv) <span class="text-red-500">*</span></label>
                    <input id="file" type="file" name="file" accept=".xlsx,.xls,.xlsm,.csv"
                        class="form-input border-dashed border-2 p-6 text-center cursor-pointer hover:border-emerald-500 @error('file') border-red-400 @enderror" required>
                    @error('file')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-3">
                    <h4 class="font-semibold text-sm text-gray-800">Petunjuk Format Excel:</h4>
                    <ul class="list-disc list-inside text-xs text-gray-600 space-y-1.5">
                        <li>Pastikan data siswa berada pada sheet pertama atau sheet bernama <strong>"Siswa"</strong>.</li>
                        <li>Baris pertama harus berupa header kolom.</li>
                        <li>Kolom wajib diisi: <strong>No. Induk</strong> (NIS/NISN), <strong>Nama</strong>, dan <strong>Kelas</strong>.</li>
                        <li>Kolom opsional: <strong>Asrama</strong>, <strong>Jenis Kelamin</strong> (L/P, default L), dan <strong>Tanggal Masuk</strong> (format tanggal YYYY-MM-DD atau angka tanggal excel).</li>
                        <li>Jika nomor induk sudah ada di database, data siswa tersebut akan diperbarui secara otomatis.</li>
                    </ul>
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                    <button type="submit" class="btn-primary">
                        Mulai Import Data
                    </button>
                    <a href="{{ route('master.siswa.index') }}" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>

    </div>
</x-layouts.app>
