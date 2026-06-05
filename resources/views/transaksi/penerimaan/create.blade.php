<x-layouts.app title="Catat Penerimaan">
    <x-slot:pageTitle>Penerimaan / Pencatatan</x-slot:pageTitle>

    <div class="space-y-5 max-w-3xl">

        <div class="flex items-center gap-4">
            <a href="{{ route('penerimaan.index') }}" class="btn-secondary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Pencatatan Penerimaan</h1>
        </div>

        {{-- STEP 1: Cari Siswa --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                    <span class="w-6 h-6 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                    Cari & Pilih Siswa
                </h3>
            </div>
            <div class="card-body space-y-4">
                <form method="GET" action="{{ route('penerimaan.catat') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <input type="text" name="cari" value="{{ request('cari') }}"
                            class="form-input w-full" placeholder="Cari nama, no. induk, atau kelas..."
                            autofocus>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Cari
                        </button>
                        @if(request('cari') || request('no_induk'))
                            <a href="{{ route('penerimaan.catat') }}" class="btn-secondary flex items-center">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>

                @if(request('no_induk') && !isset($siswa))
                    <div class="alert-error max-w-md">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>Siswa dengan No. Induk <strong>{{ request('no_induk') }}</strong> tidak ditemukan atau belum aktif di tahun ajaran ini.</p>
                    </div>
                @endif

                @if($daftarSiswa && $daftarSiswa->isNotEmpty())
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No. Induk</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th class="text-center w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($daftarSiswa as $s)
                                    @php
                                        $isTerpilih = isset($siswa) && $siswa->id === $s->id;
                                    @endphp
                                    <tr class="{{ $isTerpilih ? 'bg-emerald-50/50' : '' }}">
                                        <td class="font-mono text-xs font-medium">{{ $s->siswa->no_induk }}</td>
                                        <td class="font-medium text-gray-900">
                                            <div class="flex items-center gap-2">
                                                <span>{{ $s->siswa->nama }}</span>
                                                @if($isTerpilih)
                                                    <span class="badge-green text-xs">
                                                        Terpilih
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $s->siswa->kelas }}</td>
                                        <td class="text-center">
                                            @if($isTerpilih)
                                                <button type="button" class="btn-secondary btn-sm opacity-60 cursor-not-allowed w-full" disabled>
                                                    Terpilih
                                                </button>
                                            @else
                                                <a href="{{ route('penerimaan.catat', ['no_induk' => $s->siswa->no_induk, 'cari' => request('cari')]) }}"
                                                    class="btn-primary btn-sm w-full block text-center">
                                                    Pilih
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($daftarSiswa->hasPages())
                        <div class="pt-2">
                            {{ $daftarSiswa->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-sm font-medium">Tidak ada data siswa ditemukan.</p>
                        @if($tahunAktif)
                            <p class="text-xs text-gray-400 mt-1">Pastikan siswa sudah diaktifkan di Tahun Ajaran {{ $tahunAktif->nama }}.</p>
                        @else
                            <p class="text-xs text-amber-600 mt-1 font-medium">Belum ada Tahun Ajaran yang aktif.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @isset($siswa)
        {{-- STEP 2: Info Siswa --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                    <span class="w-6 h-6 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                    Data Siswa
                </h3>
                <span class="badge-green">Ditemukan</span>
            </div>
            <div class="card-body">
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center shrink-0">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-6 gap-y-2 text-sm flex-1">
                            <div><p class="text-gray-500">Nama</p><p class="font-semibold text-gray-900">{{ $siswa->siswa->nama }}</p></div>
                            <div><p class="text-gray-500">No. Induk</p><p class="font-mono font-semibold">{{ $siswa->siswa->no_induk }}</p></div>
                            <div><p class="text-gray-500">Kelas</p><p class="font-semibold">{{ $siswa->siswa->kelas }}</p></div>
                            <div><p class="text-gray-500">Tahun Ajaran</p><p class="font-semibold">{{ $siswa->tahunAjaran->nama }}</p></div>
                            <div><p class="text-gray-500">Tarif SPP</p><p class="font-semibold text-emerald-700">{{ format_rupiah($siswa->tarif_spp) }}</p></div>
                            @if($siswa->tunggakan_awal > 0)
                                <div>
                                    <p class="text-gray-500">Sisa Tunggakan</p>
                                    <p class="font-semibold text-amber-700">{{ format_rupiah($sisaTunggakan) }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if($siswa->tunggakan_awal > 0 && $sisaTunggakan > 0)
                    <div class="alert-warning mt-4">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>Terdapat <strong>tunggakan tahun sebelumnya</strong> sebesar
                            <strong>{{ format_rupiah($sisaTunggakan) }}</strong>.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- STEP 3: Form Pembayaran --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                    <span class="w-6 h-6 bg-emerald-600 text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                    Pilih Pembayaran
                </h3>
            </div>
            <form id="form-penerimaan" method="POST" action="{{ route('penerimaan.store') }}">
                @csrf
                <input type="hidden" name="siswa_tahun_ajaran_id" value="{{ $siswa->id }}">
                <input type="hidden" id="total-bayar-input" name="total_bayar" value="0">

                <div class="card-body space-y-5">
                    @php
                        $defaultTab = 'spp';
                    @endphp

                    {{-- Tab Navigation (Boxed Segmented Selector) --}}
                    <div class="grid grid-cols-2 gap-3 mb-2">
                        <button type="button" onclick="switchTab('spp')" id="tab-btn-spp"
                            class="tab-btn p-3 rounded-xl border-2 text-center transition-all focus:outline-none flex-1
                            border-gray-200 bg-white text-gray-700 font-medium hover:border-gray-300 hover:bg-gray-50">
                            <span class="block text-base">SPP Bulanan</span>
                            <span class="tab-desc block text-xs font-normal text-gray-500 mt-0.5">Daftar SPP per bulan</span>
                        </button>
                        <button type="button" onclick="switchTab('iuran')" id="tab-btn-iuran"
                            class="tab-btn p-3 rounded-xl border-2 text-center transition-all focus:outline-none flex-1
                            border-gray-200 bg-white text-gray-700 font-medium hover:border-gray-300 hover:bg-gray-50">
                            <span class="block text-base">Iuran / Lainnya</span>
                            <span class="tab-desc block text-xs font-normal text-gray-500 mt-0.5">Iuran & tunggakan lain</span>
                        </button>
                    </div>

                    {{-- SPP Section --}}
                    <div id="section-spp" class="space-y-3 hidden">
                        @if($tagihanSpp->isNotEmpty())
                            <div class="space-y-2">
                                @foreach($tagihanSpp as $spp)
                                    @php
                                        $lunas   = $spp->status === 'lunas';
                                        $nama    = \Carbon\Carbon::createFromDate($spp->tahun, $spp->bulan, 1)->locale('id')->isoFormat('MMMM YYYY');
                                        $nominal = $spp->status === 'lunas' ? $spp->tagihan : $spp->sisa();
                                    @endphp
                                    <label class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-all
                                        {{ $lunas ? 'border-gray-200 bg-gray-50 opacity-60 cursor-not-allowed' : 'border-gray-200 hover:border-emerald-400 hover:bg-emerald-50' }}">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" name="items[spp][]" value="{{ $spp->id }}"
                                                data-tagihan-nominal="{{ $nominal }}"
                                                {{ $lunas ? 'disabled' : '' }}
                                                class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">SPP {{ $nama }}</p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $spp->status === 'cicilan' ? 'Cicilan — Sisa bayar' : ($lunas ? 'Sudah lunas' : 'Belum dibayar') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-sm">{{ format_rupiah($nominal) }}</p>
                                            @if($lunas) <span class="badge-green text-xs">Lunas</span> @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div class="p-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                <p class="text-sm font-medium">Tidak ada tagihan SPP untuk siswa ini.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Iuran & Lainnya Section --}}
                    <div id="section-iuran" class="space-y-4 hidden">
                        {{-- Iuran --}}
                        @if($tagihanIuran->isNotEmpty())
                            <div class="space-y-2">
                                @foreach($tagihanIuran as $iuran)
                                    @php
                                        $lunas = $iuran->status === 'lunas';
                                        $nominal = $lunas ? $iuran->tagihan : $iuran->sisa();
                                    @endphp
                                    <label class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-all
                                        {{ $lunas ? 'border-gray-200 bg-gray-50 opacity-60 cursor-not-allowed' : 'border-gray-200 hover:border-emerald-400 hover:bg-emerald-50' }}">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" name="items[iuran][]" value="{{ $iuran->id }}"
                                                data-tagihan-nominal="{{ $nominal }}"
                                                {{ $lunas ? 'disabled' : '' }}
                                                class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                            <label class="flex-1 cursor-pointer">
                                                <span class="text-sm font-medium text-gray-900 block">{{ $iuran->jenisPenerimaan->nama }}</span>
                                                <span class="text-xs text-gray-500 block">
                                                    {{ $iuran->status === 'cicilan' ? 'Cicilan — Sisa bayar' : ($lunas ? 'Sudah lunas' : 'Belum dibayar') }}
                                                </span>
                                            </label>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-sm">{{ format_rupiah($nominal) }}</p>
                                            @if($lunas) <span class="badge-green text-xs">Lunas</span> @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        {{-- Tunggakan --}}
                        @if($sisaTunggakan > 0)
                            <div class="p-3 rounded-lg border border-amber-200 bg-amber-50 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" name="items[tunggakan]" value="1"
                                        data-tagihan-nominal="{{ $sisaTunggakan }}"
                                        class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Cicil/Lunasi Tunggakan</p>
                                        <p class="text-xs text-amber-700">Sisa: {{ format_rupiah($sisaTunggakan) }}</p>
                                    </div>
                                </div>
                                <div>
                                    <input type="number" name="nominal_tunggakan" value="{{ $sisaTunggakan }}"
                                        class="form-input w-36 text-right text-sm" min="1000" max="{{ $sisaTunggakan }}"
                                        step="1000" placeholder="Nominal cicil">
                                </div>
                            </div>
                        @endif

                        {{-- Jika Keduanya Kosong --}}
                        @if($tagihanIuran->isEmpty() && $sisaTunggakan == 0)
                            <div class="p-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm font-medium">Tidak ada tagihan iuran atau tunggakan.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Element dummy untuk mencegah error JS --}}
                    <div id="section-tunggakan" class="hidden"></div>

                    {{-- Total, Info, & Catatan (Hidden on load) --}}
                    <div id="section-checkout" class="space-y-5 hidden">
                        <div class="bg-gray-50 rounded-xl p-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Total Pembayaran</p>
                                <p id="total-bayar-display" class="text-2xl font-bold text-emerald-700">Rp 0</p>
                            </div>
                            <div class="text-right text-xs text-gray-400">
                                <p>Tanggal: {{ now()->format('d/m/Y') }}</p>
                                <p>Operator: {{ auth()->user()->name }}</p>
                            </div>
                        </div>

                        {{-- Catatan --}}
                        <div>
                            <label class="form-label">Catatan (opsional)</label>
                            <textarea name="catatan" rows="2" class="form-textarea"
                                placeholder="Catatan tambahan...">{{ old('catatan') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons Footer (Hidden on load) --}}
                <div id="section-actions" class="px-6 py-4 border-t border-gray-100 flex items-center gap-3 hidden">
                    <button type="submit" class="btn-primary"
                        onclick="return document.getElementById('total-bayar-input').value > 0 || (alert('Pilih minimal 1 item pembayaran'), false)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Proses Pembayaran
                    </button>
                    <a href="{{ route('penerimaan.catat') }}" class="btn-secondary">Reset</a>
                </div>
            </form>
        </div>
        @endisset

    </div>

    {{-- Script Tab Switcher --}}
    <script>
        function switchTab(tabName) {
            // Hide all sections
            const sppSec = document.getElementById('section-spp');
            const iuranSec = document.getElementById('section-iuran');
            const tunggakanSec = document.getElementById('section-tunggakan');

            if (sppSec) sppSec.classList.add('hidden');
            if (iuranSec) iuranSec.classList.add('hidden');
            if (tunggakanSec) tunggakanSec.classList.add('hidden');

            // Show selected section
            const activeSec = document.getElementById('section-' + tabName);
            if (activeSec) activeSec.classList.remove('hidden');

            // Show checkout and action sections
            const checkoutSec = document.getElementById('section-checkout');
            const actionsSec = document.getElementById('section-actions');
            if (checkoutSec) checkoutSec.classList.remove('hidden');
            if (actionsSec) actionsSec.classList.remove('hidden');

            // Reset all tab button styles to inactive
            const sppBtn = document.getElementById('tab-btn-spp');
            const iuranBtn = document.getElementById('tab-btn-iuran');

            if (sppBtn) {
                sppBtn.className = "tab-btn p-3 rounded-xl border-2 text-center transition-all focus:outline-none flex-1 border-gray-200 bg-white text-gray-700 font-medium hover:border-gray-300 hover:bg-gray-50";
                const desc = sppBtn.querySelector('.tab-desc');
                if (desc) desc.className = "tab-desc block text-xs font-normal text-gray-500 mt-0.5";
            }
            if (iuranBtn) {
                iuranBtn.className = "tab-btn p-3 rounded-xl border-2 text-center transition-all focus:outline-none flex-1 border-gray-200 bg-white text-gray-700 font-medium hover:border-gray-300 hover:bg-gray-50";
                const desc = iuranBtn.querySelector('.tab-desc');
                if (desc) desc.className = "tab-desc block text-xs font-normal text-gray-500 mt-0.5";
            }

            // Set active tab button style
            const activeBtn = document.getElementById('tab-btn-' + tabName);
            if (activeBtn) {
                activeBtn.className = "tab-btn p-3 rounded-xl border-2 text-center transition-all focus:outline-none border-emerald-600 bg-emerald-50 text-emerald-800 font-semibold shadow-sm flex-1";
                const desc = activeBtn.querySelector('.tab-desc');
                if (desc) desc.className = "tab-desc block text-xs font-normal text-emerald-600 mt-0.5";
            }
        }
    </script>
</x-layouts.app>
