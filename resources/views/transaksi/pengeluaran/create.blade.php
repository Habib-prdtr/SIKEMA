<x-layouts.app title="Catat Pengeluaran">
    <x-slot:pageTitle>Pengeluaran / Pencatatan</x-slot:pageTitle>

    <div class="max-w-xl space-y-5">

        <div class="flex items-center gap-4">
            <a href="{{ route('pengeluaran.index') }}" class="btn-secondary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Catat Pengeluaran</h1>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900">Form Pengeluaran Kas</h3>
            </div>
            <form method="POST" action="{{ route('pengeluaran.store') }}" class="card-body space-y-5">
                @csrf

                <div>
                    <label for="pos_biaya_id" class="form-label">Pos Biaya <span class="text-red-500">*</span></label>
                    <select id="pos_biaya_id" name="pos_biaya_id"
                        class="form-select @error('pos_biaya_id') border-red-400 @enderror" required>
                        <option value="">-- Pilih Pos Biaya --</option>
                        @foreach($posList as $pos)
                            <option value="{{ $pos->id }}" {{ old('pos_biaya_id') == $pos->id ? 'selected' : '' }}>
                                {{ $pos->nama }}
                                @if($pos->anggaran > 0)
                                    (Anggaran: {{ format_rupiah($pos->anggaran) }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('pos_biaya_id')<p class="form-error">{{ $message }}</p>@enderror
                    @if($posList->isEmpty())
                        <p class="text-xs text-amber-600 mt-1">
                            Belum ada pos biaya.
                            <a href="{{ route('master.pos-biaya.index') }}" class="underline">Tambah pos biaya</a> terlebih dahulu.
                        </p>
                    @endif
                </div>

                <div>
                    <label for="jumlah" class="form-label">Jumlah <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 inset-y-0 flex items-center text-gray-500 text-sm">Rp</span>
                        <input id="jumlah" type="number" name="jumlah"
                            value="{{ old('jumlah') }}"
                            class="form-input pl-9 @error('jumlah') border-red-400 @enderror"
                            placeholder="0" min="0" step="1000" required>
                    </div>
                    @error('jumlah')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="tanggal" class="form-label">Tanggal <span class="text-red-500">*</span></label>
                    <input id="tanggal" type="date" name="tanggal"
                        value="{{ old('tanggal', date('Y-m-d')) }}"
                        class="form-input @error('tanggal') border-red-400 @enderror" required>
                    @error('tanggal')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="keterangan" class="form-label">Keterangan <span class="text-red-500">*</span></label>
                    <textarea id="keterangan" name="keterangan" rows="3"
                        class="form-textarea @error('keterangan') border-red-400 @enderror"
                        placeholder="Keterangan pengeluaran..." required>{{ old('keterangan') }}</textarea>
                    @error('keterangan')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="pt-2 border-t border-gray-100 flex items-center gap-3">
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Pengeluaran
                    </button>
                    <a href="{{ route('pengeluaran.index') }}" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>

    </div>
</x-layouts.app>
