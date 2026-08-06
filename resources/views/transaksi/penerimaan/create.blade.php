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

                <div id="catat-siswa-search-results">
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
                                    <tr data-no-induk="{{ $s->siswa->no_induk }}" class="siswa-row-catat {{ $isTerpilih ? 'bg-emerald-50/50' : '' }}">
                                        <td class="font-mono text-xs font-medium">{{ $s->siswa->no_induk }}</td>
                                        <td class="font-medium text-gray-900">
                                            <div class="flex items-center gap-2">
                                                <span>{{ $s->siswa->nama }}</span>
                                                <span class="badge-terpilih-catat badge-green text-xs {{ $isTerpilih ? '' : 'hidden' }}">
                                                    Terpilih
                                                </span>
                                            </div>
                                        </td>
                                        <td>{{ $s->siswa->kelas }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('penerimaan.catat', ['no_induk' => $s->siswa->no_induk, 'cari' => request('cari')]) }}"
                                                data-no-induk="{{ $s->siswa->no_induk }}"
                                                class="btn-pilih-siswa btn-sm w-full block text-center transition-all {{ $isTerpilih ? 'bg-emerald-50 text-emerald-700 border-2 border-emerald-500 font-semibold' : 'bg-emerald-600 hover:bg-emerald-700 text-white font-medium shadow-sm' }}">
                                                {{ $isTerpilih ? 'Terpilih' : 'Pilih' }}
                                            </a>
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
        </div>

        {{-- Container Step 2 & Step 3 --}}
        <div id="container-detail-transaksi" class="space-y-5 {{ isset($siswa) ? '' : 'hidden' }}">
            {{-- STEP 2: Info Siswa --}}
            <div class="card" id="card-data-siswa">
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
                                <div><p class="text-gray-500">Nama</p><p id="detail-nama" class="font-semibold text-gray-900">{{ $siswa->siswa->nama ?? '' }}</p></div>
                                <div><p class="text-gray-500">No. Induk</p><p id="detail-no-induk" class="font-mono font-semibold">{{ $siswa->siswa->no_induk ?? '' }}</p></div>
                                <div><p class="text-gray-500">Kelas</p><p id="detail-kelas" class="font-semibold">{{ $siswa->siswa->kelas ?? '' }}</p></div>
                                <div><p class="text-gray-500">Tahun Ajaran</p><p id="detail-tahun-ajaran" class="font-semibold">{{ $siswa->tahunAjaran->nama ?? '' }}</p></div>
                                <div><p class="text-gray-500">Tarif SPP</p><p id="detail-tarif-spp" class="font-semibold text-emerald-700">{{ isset($siswa) ? format_rupiah($siswa->tarif_spp) : '' }}</p></div>
                                <div id="detail-sisa-tunggakan-wrapper" class="{{ ($siswa->tunggakan_awal ?? 0) > 0 ? '' : 'hidden' }}">
                                    <p class="text-gray-500">Sisa Tunggakan</p>
                                    <p id="detail-sisa-tunggakan" class="font-semibold text-amber-700">{{ format_rupiah($sisaTunggakan ?? 0) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="alert-tunggakan" class="alert-warning mt-4 {{ (($siswa->tunggakan_awal ?? 0) > 0 && ($sisaTunggakan ?? 0) > 0) ? '' : 'hidden' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>Terdapat <strong>tunggakan tahun sebelumnya</strong> sebesar
                            <strong id="text-sisa-tunggakan-alert">{{ format_rupiah($sisaTunggakan ?? 0) }}</strong>.</p>
                    </div>
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
                <form id="form-penerimaan" novalidate method="POST" action="{{ route('penerimaan.store') }}">
                    @csrf
                    <input type="hidden" id="input-siswa-tahun-ajaran-id" name="siswa_tahun_ajaran_id" value="{{ $siswa->id ?? '' }}">
                    <input type="hidden" id="total-bayar-input" name="total_bayar" value="0">

                    <div class="card-body space-y-5">
                        @if($errors->any())
                            <div class="alert-error">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="font-semibold text-xs mb-1">Gagal memproses transaksi:</p>
                                    <ul class="list-disc list-inside text-xs space-y-0.5">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        {{-- Tab Navigation (Boxed Segmented Selector) --}}
                        <div class="grid grid-cols-3 gap-3 mb-2">
                            <button type="button" onclick="switchTab('spp')" id="tab-btn-spp"
                                class="tab-btn p-3 rounded-xl border-2 text-center transition-all focus:outline-none flex-1
                                border-emerald-600 bg-emerald-50 text-emerald-800 font-semibold shadow-sm">
                                <span class="block text-base">SPP Bulanan</span>
                                <span class="tab-desc block text-xs font-normal text-emerald-600 mt-0.5">Daftar SPP per bulan</span>
                            </button>
                            <button type="button" onclick="switchTab('tabungan_wajib')" id="tab-btn-tabungan_wajib"
                                class="tab-btn p-3 rounded-xl border-2 text-center transition-all focus:outline-none flex-1
                                border-gray-200 bg-white text-gray-700 font-medium hover:border-gray-300 hover:bg-gray-50">
                                <span class="block text-base">Tabungan Wajib</span>
                                <span class="tab-desc block text-xs font-normal text-gray-500 mt-0.5">Tabungan wajib</span>
                            </button>
                            <button type="button" onclick="switchTab('iuran')" id="tab-btn-iuran"
                                class="tab-btn p-3 rounded-xl border-2 text-center transition-all focus:outline-none flex-1
                                border-gray-200 bg-white text-gray-700 font-medium hover:border-gray-300 hover:bg-gray-50">
                                <span class="block text-base">Iuran / Lainnya</span>
                                <span class="tab-desc block text-xs font-normal text-gray-500 mt-0.5">Iuran & tunggakan lain</span>
                            </button>
                        </div>

                        {{-- SPP Section --}}
                        <div id="section-spp" class="space-y-3">
                            <div id="list-items-spp" class="space-y-2">
                                @if(isset($tagihanSpp) && $tagihanSpp->isNotEmpty())
                                    @foreach($tagihanSpp as $spp)
                                        @php
                                            $lunas   = $spp->status === 'lunas';
                                            $nama    = \Carbon\Carbon::createFromDate($spp->tahun, $spp->bulan, 1)->locale('id')->isoFormat('MMMM YYYY');
                                            $nominal = $spp->status === 'lunas' ? $spp->tagihan : $spp->sisa();
                                            $hasDispensasi = isset($siswa) && $spp->tagihan < $siswa->tarif_spp;
                                            $potongan = $hasDispensasi ? ($siswa->tarif_spp - $spp->tagihan) : 0;
                                            $namaDispensasi = $siswa->dispensasi->nama ?? 'Dispensasi';
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
                                                    @if($hasDispensasi)
                                                        <p class="text-xs text-purple-700 font-medium mt-0.5 flex items-center gap-1">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600 inline-block"></span>
                                                            Potongan Dispensasi ({{ $namaDispensasi }}): -{{ format_rupiah($potongan) }} (Normal: {{ format_rupiah($siswa->tarif_spp) }})
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-semibold text-sm text-gray-900">{{ format_rupiah($nominal) }}</p>
                                                @if($lunas)
                                                    <span class="badge-green text-xs">Lunas</span>
                                                @elseif($hasDispensasi)
                                                    <span class="inline-block text-xs font-semibold text-purple-700 bg-purple-50 border border-purple-200 px-2 py-0.5 rounded">{{ $namaDispensasi }}</span>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                @else
                                    <div class="p-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                        <p class="text-sm font-medium">Tidak ada tagihan SPP untuk siswa ini.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Tabungan Wajib Section --}}
                        <div id="section-tabungan_wajib" class="space-y-3 hidden">
                            <div id="list-items-tabungan-wajib" class="space-y-2">
                                @if(isset($tagihanTabunganWajib) && $tagihanTabunganWajib->isNotEmpty())
                                    @foreach($tagihanTabunganWajib as $tw)
                                        @php
                                            $lunas   = $tw->status === 'lunas';
                                            $nama    = \Carbon\Carbon::createFromDate($tw->tahun, $tw->bulan, 1)->locale('id')->isoFormat('MMMM YYYY');
                                            $nominal = $tw->status === 'lunas' ? $tw->tagihan : $tw->sisa();
                                        @endphp
                                        <label class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-all
                                            {{ $lunas ? 'border-gray-200 bg-gray-50 opacity-60 cursor-not-allowed' : 'border-gray-200 hover:border-emerald-400 hover:bg-emerald-50' }}">
                                            <div class="flex items-center gap-3">
                                                <input type="checkbox" name="items[tabungan_wajib][]" value="{{ $tw->id }}"
                                                    data-tagihan-nominal="{{ $nominal }}"
                                                    {{ $lunas ? 'disabled' : '' }}
                                                    class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">Tabungan Wajib {{ $nama }}</p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ $tw->status === 'cicilan' ? 'Cicilan — Sisa bayar' : ($lunas ? 'Sudah lunas' : 'Belum dibayar') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-semibold text-sm text-gray-900">{{ format_rupiah($nominal) }}</p>
                                                @if($lunas)
                                                    <span class="badge-green text-xs">Lunas</span>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                @else
                                    <div class="p-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                        <p class="text-sm font-medium">Tidak ada tagihan Tabungan Wajib untuk siswa ini.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Iuran & Lainnya Section --}}
                        <div id="section-iuran" class="space-y-4 hidden">
                            <div id="list-items-iuran" class="space-y-2">
                                @if(isset($tagihanIuran) && $tagihanIuran->isNotEmpty())
                                    @foreach($tagihanIuran as $iuran)
                                        @php
                                            $lunas = $iuran->status === 'lunas';
                                            $nominal = $lunas ? $iuran->tagihan : $iuran->sisa();
                                            $origTarif = $iuran->jenisPenerimaan->tarif ?? $iuran->tagihan;
                                            $isIuranDispensasi = isset($siswa) && $siswa->dispensasi && $siswa->dispensasi->jenis_penerimaan_id == $iuran->jenis_penerimaan_id;
                                            $hasDispensasi = $isIuranDispensasi && ($iuran->tagihan < $origTarif);
                                            $potongan = $hasDispensasi ? ($origTarif - $iuran->tagihan) : 0;
                                            $namaDispensasi = $siswa->dispensasi->nama ?? 'Dispensasi';
                                        @endphp
                                        <label class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-all
                                            {{ $lunas ? 'border-gray-200 bg-gray-50 opacity-60 cursor-not-allowed' : 'border-gray-200 hover:border-emerald-400 hover:bg-emerald-50' }}">
                                            <div class="flex items-center gap-3">
                                                <input type="checkbox" name="items[iuran][]" value="{{ $iuran->id }}"
                                                    data-tagihan-nominal="{{ $nominal }}"
                                                    {{ $lunas ? 'disabled' : '' }}
                                                    class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                                <div class="flex-1">
                                                    <span class="text-sm font-medium text-gray-900 block">{{ $iuran->jenisPenerimaan->nama }}</span>
                                                    <span class="text-xs text-gray-500 block">
                                                        {{ $iuran->status === 'cicilan' ? 'Cicilan — Sisa bayar' : ($lunas ? 'Sudah lunas' : 'Belum dibayar') }}
                                                    </span>
                                                    @if($hasDispensasi)
                                                        <p class="text-xs text-purple-700 font-medium mt-0.5 flex items-center gap-1">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600 inline-block"></span>
                                                            Potongan Dispensasi ({{ $namaDispensasi }}): -{{ format_rupiah($potongan) }} (Normal: {{ format_rupiah($origTarif) }})
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-semibold text-sm text-gray-900">{{ format_rupiah($nominal) }}</p>
                                                @if($lunas)
                                                    <span class="badge-green text-xs">Lunas</span>
                                                @elseif($hasDispensasi)
                                                    <span class="inline-block text-xs font-semibold text-purple-700 bg-purple-50 border border-purple-200 px-2 py-0.5 rounded">{{ $namaDispensasi }}</span>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                @else
                                    <div class="p-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                        <p class="text-sm font-medium">Tidak ada tagihan iuran untuk siswa ini.</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Tunggakan --}}
                            <div id="wrapper-tunggakan" class="{{ ($sisaTunggakan ?? 0) > 0 ? '' : 'hidden' }}">
                                <div class="p-3 rounded-lg border border-amber-200 bg-amber-50 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="items[tunggakan]" value="1"
                                            data-tagihan-nominal="{{ $sisaTunggakan ?? 0 }}"
                                            class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Cicil/Lunasi Tunggakan</p>
                                            <p class="text-xs text-amber-700">Sisa: <span id="text-sisa-tunggakan-iuran">{{ format_rupiah($sisaTunggakan ?? 0) }}</span></p>
                                        </div>
                                    </div>
                                    <div>
                                        <input type="number" name="nominal_tunggakan" value="{{ $sisaTunggakan ?? 0 }}"
                                            class="form-input w-36 text-right text-sm" min="0" max="{{ $sisaTunggakan ?? 0 }}"
                                            step="1000" placeholder="Nominal cicil">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Element dummy untuk mencegah error JS --}}
                        <div id="section-tunggakan" class="hidden"></div>

                        {{-- Custom / Penerimaan Tambahan --}}
                        <div id="section-custom-penerimaan" class="bg-gradient-to-r from-emerald-50/80 to-teal-50/80 rounded-xl p-4 border border-emerald-200 space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-emerald-200/60">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-sm shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider truncate">Penerimaan Tambahan / Custom</h4>
                                        <p class="text-[11px] text-gray-500 truncate">Item penerimaan khusus (misal: Seragam, Buku, Infaq Sukarela, dll)</p>
                                    </div>
                                </div>
                                <button type="button" onclick="addCustomPenerimaanRow()" class="btn-secondary btn-sm text-xs flex items-center gap-1.5 border-emerald-300 bg-white hover:bg-emerald-50 text-emerald-800 font-semibold shadow-sm shrink-0 self-start sm:self-auto">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span>+ Tambah Item</span>
                                </button>
                            </div>

                            <div id="custom-penerimaan-list" class="space-y-2">
                                {{-- Custom rows dynamically inserted here --}}
                            </div>
                        </div>

                        {{-- Total, Info, & Catatan --}}
                        <div id="section-checkout" class="space-y-5">
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

                    {{-- Action Buttons Footer --}}
                    <div id="section-actions" class="px-6 py-4 border-t border-gray-100 flex items-center gap-3">
                        <button type="button" onclick="showModalKonfirmasiCatat()" id="btn-submit-penerimaan" class="btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Proses Pembayaran</span>
                        </button>
                        <a href="{{ route('penerimaan.catat') }}" class="btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- Modal Konfirmasi Pembayaran --}}
    <div id="modal-konfirmasi-penerimaan" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-md hidden opacity-0 transition-all duration-200">
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 max-w-md w-full mx-4 overflow-hidden transform scale-95 transition-all duration-200" id="modal-konfirmasi-card">
            
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-emerald-600 text-white">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-base leading-tight">Konfirmasi Pembayaran</h3>
                        <p class="text-xs text-emerald-100 font-normal">Periksa rincian sebelum menyimpan</p>
                    </div>
                </div>
                <button type="button" onclick="closeModalKonfirmasi()" class="text-emerald-100 hover:text-white focus:outline-none p-1 rounded-lg hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            {{-- Body --}}
            <div class="p-6 space-y-4 text-sm bg-white">
                
                {{-- Student Box --}}
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-2">
                    <div class="flex items-center justify-between text-xs text-gray-500 pb-2 border-b border-gray-200/60">
                        <span class="font-semibold uppercase tracking-wider text-gray-400">Informasi Siswa</span>
                        <span id="modal-no-induk-siswa" class="font-mono bg-white px-2 py-0.5 rounded border border-gray-200 font-semibold text-gray-700"></span>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <div>
                            <h4 id="modal-nama-siswa" class="font-bold text-gray-900 text-base leading-tight"></h4>
                            <p id="modal-kelas-siswa" class="text-xs text-emerald-600 font-semibold mt-0.5"></p>
                        </div>
                        <div class="shrink-0 bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-1 rounded-full">
                            Aktif
                        </div>
                    </div>
                </div>

                {{-- Selected Items --}}
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Item Pembayaran Terpilih</p>
                    <div id="modal-list-items" class="space-y-2 max-h-52 overflow-y-auto pr-1">
                    </div>
                </div>

                {{-- Catatan --}}
                <div id="modal-catatan-wrapper" class="hidden text-xs bg-amber-50/80 border border-amber-200 p-3 rounded-xl">
                    <span class="font-semibold text-amber-800 block mb-0.5">Catatan Tambahan:</span>
                    <span id="modal-catatan-text" class="text-amber-900 font-normal"></span>
                </div>

                {{-- Total Bayar Footer Box --}}
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-emerald-800 font-medium">Total Pembayaran</p>
                        <p class="text-xs text-gray-500">Tunai / Transfer</p>
                    </div>
                    <div class="text-right">
                        <span id="modal-total-bayar" class="text-2xl font-black text-emerald-700 tracking-tight"></span>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModalKonfirmasi()" class="btn-secondary">
                    Batal
                </button>
                <button type="button" id="btn-submit-final-modal" onclick="submitFormPenerimaanFinal()" class="btn-primary shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Ya, Proses Pembayaran</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Script Tab Switcher & AJAX --}}
    <script>
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        }

        let customRowIndex = 0;

        function addCustomPenerimaanRow(nama = '', nominal = '') {
            const listContainer = document.getElementById('custom-penerimaan-list');
            if (!listContainer) return;

            const rowId = `custom-row-${customRowIndex}`;
            const div = document.createElement('div');
            div.id = rowId;
            div.className = "custom-item-row flex flex-wrap sm:flex-nowrap items-center gap-3 bg-white p-3 rounded-xl border border-emerald-200 shadow-sm transition-all";

            div.innerHTML = `
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="custom_items[${customRowIndex}][nama]" value="${nama}"
                        class="form-input text-xs input-custom-nama font-medium"
                        placeholder="Nama Penerimaan (misal: Seragam, Buku, Infaq...)" required>
                </div>
                <div class="w-40 shrink-0">
                    <div class="relative flex items-center">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400 pointer-events-none select-none z-10">Rp</span>
                        <input type="number" name="custom_items[${customRowIndex}][nominal]" value="${nominal}"
                            class="form-input text-xs pl-9 pr-3 font-bold text-gray-900 input-custom-nominal w-full"
                            min="1" step="500" placeholder="Nominal" required>
                    </div>
                </div>
                <button type="button" onclick="removeCustomPenerimaanRow('${rowId}')"
                    class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors shrink-0" title="Hapus Item">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            `;

            listContainer.appendChild(div);
            customRowIndex++;

            div.querySelectorAll('input').forEach(input => {
                input.addEventListener('input', updateTotalBayar);
                input.addEventListener('change', updateTotalBayar);
            });

            updateTotalBayar();
        }

        function removeCustomPenerimaanRow(rowId) {
            const row = document.getElementById(rowId);
            if (row) {
                row.remove();
                updateTotalBayar();
            }
        }

        function updateTotalBayar() {
            let total = 0;
            const checkedCheckboxes = document.querySelectorAll('#form-penerimaan input[type="checkbox"]:checked');
            
            // Highlight selected labels
            document.querySelectorAll('#form-penerimaan label').forEach(label => {
                const cb = label.querySelector('input[type="checkbox"]');
                if (cb && !cb.disabled) {
                    if (cb.checked) {
                        label.classList.add('border-emerald-500', 'bg-emerald-50/70', 'ring-1', 'ring-emerald-500');
                        label.classList.remove('border-gray-200');
                    } else {
                        label.classList.remove('border-emerald-500', 'bg-emerald-50/70', 'ring-1', 'ring-emerald-500');
                        label.classList.add('border-gray-200');
                    }
                }
            });

            checkedCheckboxes.forEach(cb => {
                if (cb.name === 'items[tunggakan]') {
                    const nominalInput = document.querySelector('input[name="nominal_tunggakan"]');
                    total += parseInt(nominalInput?.value || 0, 10);
                } else if (cb.name === 'items[tabungan_wajib]') {
                    const nominalInput = document.querySelector('input[name="nominal_tabungan_wajib"]');
                    total += parseInt(nominalInput?.value || 0, 10);
                } else {
                    total += parseInt(cb.dataset.tagihanNominal || 0, 10);
                }
            });

            // Add Custom items (only if visible on SPP tab)
            const customSec = document.getElementById('section-custom-penerimaan');
            if (customSec && !customSec.classList.contains('hidden')) {
                document.querySelectorAll('.input-custom-nominal').forEach(input => {
                    const val = parseInt(input.value || 0, 10);
                    if (val > 0) {
                        total += val;
                    }
                });
            }

            document.getElementById('total-bayar-input').value = total;
            document.getElementById('total-bayar-display').innerText = formatRupiah(total);
        }

        function switchTab(tabName) {
            const sppSec = document.getElementById('section-spp');
            const twSec = document.getElementById('section-tabungan_wajib');
            const iuranSec = document.getElementById('section-iuran');
            const tunggakanSec = document.getElementById('section-tunggakan');
            const customSec = document.getElementById('section-custom-penerimaan');

            if (sppSec) sppSec.classList.add('hidden');
            if (twSec) twSec.classList.add('hidden');
            if (iuranSec) iuranSec.classList.add('hidden');
            if (tunggakanSec) tunggakanSec.classList.add('hidden');

            const activeSec = document.getElementById('section-' + tabName);
            if (activeSec) activeSec.classList.remove('hidden');

            if (customSec) {
                if (tabName === 'spp') {
                    customSec.classList.remove('hidden');
                } else {
                    customSec.classList.add('hidden');
                }
            }

            const checkoutSec = document.getElementById('section-checkout');
            const actionsSec = document.getElementById('section-actions');
            if (checkoutSec) checkoutSec.classList.remove('hidden');
            if (actionsSec) actionsSec.classList.remove('hidden');

            const sppBtn = document.getElementById('tab-btn-spp');
            const twBtn = document.getElementById('tab-btn-tabungan_wajib');
            const iuranBtn = document.getElementById('tab-btn-iuran');

            [sppBtn, twBtn, iuranBtn].forEach(btn => {
                if (btn) {
                    btn.className = "tab-btn p-3 rounded-xl border-2 text-center transition-all focus:outline-none flex-1 border-gray-200 bg-white text-gray-700 font-medium hover:border-gray-300 hover:bg-gray-50";
                    const desc = btn.querySelector('.tab-desc');
                    if (desc) desc.className = "tab-desc block text-xs font-normal text-gray-500 mt-0.5";
                }
            });

            const activeBtn = document.getElementById('tab-btn-' + tabName);
            if (activeBtn) {
                activeBtn.className = "tab-btn p-3 rounded-xl border-2 text-center transition-all focus:outline-none border-emerald-600 bg-emerald-50 text-emerald-800 font-semibold shadow-sm flex-1";
                const desc = activeBtn.querySelector('.tab-desc');
                if (desc) desc.className = "tab-desc block text-xs font-normal text-emerald-600 mt-0.5";
            }

            updateTotalBayar();
        }

        function renderDetailSiswa(data) {
            const s = data.siswa;
            document.getElementById('detail-nama').innerText = s.nama;
            document.getElementById('detail-no-induk').innerText = s.no_induk;
            document.getElementById('detail-kelas').innerText = s.kelas;
            document.getElementById('detail-tahun-ajaran').innerText = s.tahun_ajaran;
            document.getElementById('detail-tarif-spp').innerText = formatRupiah(s.tarif_spp);
            document.getElementById('input-siswa-tahun-ajaran-id').value = s.id;

            // Tabungan Wajib
            if (s.tarif_tabungan_wajib !== undefined) {
                const textTw = document.getElementById('text-tarif-tabungan-wajib');
                const inputTw = document.getElementById('input-nominal-tabungan-wajib');
                const cbTw = document.getElementById('checkbox-tabungan-wajib');
                if (textTw) textTw.innerText = formatRupiah(s.tarif_tabungan_wajib);
                if (inputTw) inputTw.value = s.tarif_tabungan_wajib;
                if (cbTw) cbTw.dataset.tagihanNominal = s.tarif_tabungan_wajib;
            }

            const tunggakanWrapper = document.getElementById('detail-sisa-tunggakan-wrapper');
            const alertTunggakan = document.getElementById('alert-tunggakan');
            const wrapperTunggakan = document.getElementById('wrapper-tunggakan');

            if (s.tunggakan_awal > 0 && data.sisaTunggakan > 0) {
                if (tunggakanWrapper) {
                    tunggakanWrapper.classList.remove('hidden');
                    document.getElementById('detail-sisa-tunggakan').innerText = formatRupiah(data.sisaTunggakan);
                }
                if (alertTunggakan) {
                    alertTunggakan.classList.remove('hidden');
                    document.getElementById('text-sisa-tunggakan-alert').innerText = formatRupiah(data.sisaTunggakan);
                }
                if (wrapperTunggakan) {
                    wrapperTunggakan.classList.remove('hidden');
                    document.getElementById('text-sisa-tunggakan-iuran').innerText = formatRupiah(data.sisaTunggakan);
                }
            } else {
                if (tunggakanWrapper) tunggakanWrapper.classList.add('hidden');
                if (alertTunggakan) alertTunggakan.classList.add('hidden');
                if (wrapperTunggakan) wrapperTunggakan.classList.add('hidden');
            }

            // Render SPP items
            const sppListEl = document.getElementById('list-items-spp');
            if (data.tagihanSpp && data.tagihanSpp.length > 0) {
                let html = '';
                data.tagihanSpp.forEach(spp => {
                    const lunas = spp.lunas;
                    const nominal = spp.nominal;
                    const hasDispensasi = spp.hasDispensasi;
                    const potongan = spp.potongan;
                    const namaDispensasi = spp.namaDispensasi || 'Dispensasi';

                    html += `
                    <label class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-all ${lunas ? 'border-gray-200 bg-gray-50 opacity-60 cursor-not-allowed' : 'border-gray-200 hover:border-emerald-400 hover:bg-emerald-50'}">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="items[spp][]" value="${spp.id}"
                                data-tagihan-nominal="${nominal}" ${lunas ? 'disabled' : ''}
                                class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            <div>
                                <p class="text-sm font-medium text-gray-900">SPP ${spp.nama}</p>
                                <p class="text-xs text-gray-500">
                                    ${spp.status === 'cicilan' ? 'Cicilan — Sisa bayar' : (lunas ? 'Sudah lunas' : 'Belum dibayar')}
                                </p>
                                ${hasDispensasi ? `
                                    <p class="text-xs text-purple-700 font-medium mt-0.5 flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-600 inline-block"></span>
                                        Potongan Dispensasi (${namaDispensasi}): -${formatRupiah(potongan)} (Normal: ${formatRupiah(s.tarif_spp)})
                                    </p>
                                ` : ''}
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-sm text-gray-900">${formatRupiah(nominal)}</p>
                            ${lunas ? '<span class="badge-green text-xs">Lunas</span>' : (hasDispensasi ? `<span class="inline-block text-xs font-semibold text-purple-700 bg-purple-50 border border-purple-200 px-2 py-0.5 rounded">${namaDispensasi}</span>` : '')}
                        </div>
                    </label>`;
                });
                sppListEl.innerHTML = html;
            } else {
                sppListEl.innerHTML = '<div class="p-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200"><p class="text-sm font-medium">Tidak ada tagihan SPP untuk siswa ini.</p></div>';
            }

            // Render Tabungan Wajib items
            const twListEl = document.getElementById('list-items-tabungan-wajib');
            if (data.tagihanTabunganWajib && data.tagihanTabunganWajib.length > 0) {
                let html = '';
                data.tagihanTabunganWajib.forEach(tw => {
                    const lunas = tw.lunas;
                    const nominal = tw.nominal;

                    html += `
                    <label class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-all ${lunas ? 'border-gray-200 bg-gray-50 opacity-60 cursor-not-allowed' : 'border-gray-200 hover:border-emerald-400 hover:bg-emerald-50'}">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="items[tabungan_wajib][]" value="${tw.id}"
                                data-tagihan-nominal="${nominal}" ${lunas ? 'disabled' : ''}
                                class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Tabungan Wajib ${tw.nama}</p>
                                <p class="text-xs text-gray-500">
                                    ${tw.status === 'cicilan' ? 'Cicilan — Sisa bayar' : (lunas ? 'Sudah lunas' : 'Belum dibayar')}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-sm text-gray-900">${formatRupiah(nominal)}</p>
                            ${lunas ? '<span class="badge-green text-xs">Lunas</span>' : ''}
                        </div>
                    </label>`;
                });
                twListEl.innerHTML = html;
            } else {
                twListEl.innerHTML = '<div class="p-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200"><p class="text-sm font-medium">Tidak ada tagihan Tabungan Wajib untuk siswa ini.</p></div>';
            }

            // Render Iuran items
            const iuranListEl = document.getElementById('list-items-iuran');
            if (data.tagihanIuran && data.tagihanIuran.length > 0) {
                let html = '';
                data.tagihanIuran.forEach(iuran => {
                    const lunas = iuran.lunas;
                    const nominal = iuran.nominal;
                    const hasDispensasi = iuran.hasDispensasi;
                    const potongan = iuran.potongan;
                    const namaDispensasi = iuran.namaDispensasi || 'Dispensasi';
                    html += `
                    <label class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-all ${lunas ? 'border-gray-200 bg-gray-50 opacity-60 cursor-not-allowed' : 'border-gray-200 hover:border-emerald-400 hover:bg-emerald-50'}">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="items[iuran][]" value="${iuran.id}"
                                data-tagihan-nominal="${nominal}" ${lunas ? 'disabled' : ''}
                                class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-900 block">${iuran.nama}</span>
                                <span class="text-xs text-gray-500 block">
                                    ${iuran.status === 'cicilan' ? 'Cicilan — Sisa bayar' : (lunas ? 'Sudah lunas' : 'Belum dibayar')}
                                </span>
                                ${hasDispensasi ? `
                                    <p class="text-xs text-purple-700 font-medium mt-0.5 flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-600 inline-block"></span>
                                        Potongan Dispensasi (${namaDispensasi}): -${formatRupiah(potongan)}
                                    </p>
                                ` : ''}
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-sm text-gray-900">${formatRupiah(nominal)}</p>
                            ${lunas ? '<span class="badge-green text-xs">Lunas</span>' : (hasDispensasi ? `<span class="inline-block text-xs font-semibold text-purple-700 bg-purple-50 border border-purple-200 px-2 py-0.5 rounded">${namaDispensasi}</span>` : '')}
                        </div>
                    </label>`;
                });
                iuranListEl.innerHTML = html;
            } else {
                iuranListEl.innerHTML = '<div class="p-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200"><p class="text-sm font-medium">Tidak ada tagihan iuran untuk siswa ini.</p></div>';
            }

            const container = document.getElementById('container-detail-transaksi');
            container.classList.remove('hidden');

            switchTab('spp');
            updateTotalBayar();
        }

        function updateCatatSelectedStudentRows(selectedNoInduk) {
            document.querySelectorAll('.siswa-row-catat').forEach(tr => {
                const isSelected = tr.dataset.noInduk === selectedNoInduk;

                if (isSelected) {
                    tr.classList.add('bg-emerald-50/50');
                } else {
                    tr.classList.remove('bg-emerald-50/50');
                }

                const badge = tr.querySelector('.badge-terpilih-catat');
                if (badge) {
                    if (isSelected) {
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }

                const btn = tr.querySelector('.btn-pilih-siswa');
                if (btn) {
                    if (isSelected) {
                        btn.innerText = 'Terpilih';
                        btn.className = "btn-pilih-siswa btn-sm w-full block text-center transition-all bg-emerald-50 text-emerald-700 border-2 border-emerald-500 font-semibold";
                    } else {
                        btn.innerText = 'Pilih';
                        btn.className = "btn-pilih-siswa btn-sm w-full block text-center transition-all bg-emerald-600 hover:bg-emerald-700 text-white font-medium shadow-sm";
                    }
                }
            });
        }

        function showModalKonfirmasiCatat() {
            const selectedItems = document.querySelectorAll('#form-penerimaan input[type="checkbox"]:checked');
            const customSec = document.getElementById('section-custom-penerimaan');
            let hasValidCustom = false;
            if (customSec && !customSec.classList.contains('hidden')) {
                document.querySelectorAll('.custom-item-row').forEach(row => {
                    const customNama = row.querySelector('.input-custom-nama')?.value?.trim();
                    const customNominal = parseInt(row.querySelector('.input-custom-nominal')?.value || 0, 10);
                    if (customNama && customNominal > 0) {
                        hasValidCustom = true;
                    }
                });
            }

            if (selectedItems.length === 0 && !hasValidCustom) {
                alert('Silakan centang tagihan atau isi minimal 1 penerimaan tambahan.');
                return;
            }
            const totalBayar = parseInt(document.getElementById('total-bayar-input')?.value || 0, 10);

            // Student info
            const nama = document.getElementById('detail-nama')?.innerText || '-';
            const noInduk = document.getElementById('detail-no-induk')?.innerText || '';
            const kelas = document.getElementById('detail-kelas')?.innerText || '';

            document.getElementById('modal-nama-siswa').innerText = nama;
            document.getElementById('modal-no-induk-siswa').innerText = noInduk;
            document.getElementById('modal-kelas-siswa').innerText = kelas ? 'Kelas ' + kelas : '';
            document.getElementById('modal-total-bayar').innerText = formatRupiah(totalBayar);

            // Collect selected items
            let itemsHtml = '';
            document.querySelectorAll('#form-penerimaan input[type="checkbox"]:checked').forEach(cb => {
                let itemNama = '';
                let itemNominal = 0;

                const parentLabel = cb.closest('label');
                if (cb.name === 'items[tunggakan]') {
                    itemNama = 'Cicilan / Lunasi Tunggakan';
                    const nominalInput = document.querySelector('input[name="nominal_tunggakan"]');
                    itemNominal = parseInt(nominalInput?.value || 0, 10);
                } else if (cb.name === 'items[tabungan_wajib]') {
                    itemNama = 'Tabungan Wajib';
                    const nominalInput = document.querySelector('input[name="nominal_tabungan_wajib"]');
                    itemNominal = parseInt(nominalInput?.value || 0, 10);
                } else if (parentLabel) {
                    const namaEl = parentLabel.querySelector('p.font-medium, span.font-medium, p.font-semibold');
                    itemNama = namaEl ? namaEl.innerText.trim() : 'Item Pembayaran';
                    itemNominal = parseInt(cb.dataset.tagihanNominal || 0, 10);
                }

                itemsHtml += `
                    <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-200 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                            <span class="font-semibold text-gray-800">${itemNama}</span>
                        </div>
                        <span class="font-bold text-gray-900 text-sm">${formatRupiah(itemNominal)}</span>
                    </div>
                `;
            });

            // Collect Custom items (only if visible)
            if (customSec && !customSec.classList.contains('hidden')) {
                document.querySelectorAll('.custom-item-row').forEach(row => {
                    const customNama = row.querySelector('.input-custom-nama')?.value?.trim();
                    const customNominal = parseInt(row.querySelector('.input-custom-nominal')?.value || 0, 10);
                    if (customNama && customNominal > 0) {
                        itemsHtml += `
                            <div class="flex items-center justify-between p-3 rounded-xl bg-teal-50 border border-teal-200 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-teal-500 shrink-0"></span>
                                    <div>
                                        <span class="font-semibold text-gray-800">${customNama}</span>
                                        <span class="text-[10px] text-teal-600 block font-normal">Penerimaan Tambahan</span>
                                    </div>
                                </div>
                                <span class="font-bold text-gray-900 text-sm">${formatRupiah(customNominal)}</span>
                            </div>
                        `;
                    }
                });
            }

            document.getElementById('modal-list-items').innerHTML = itemsHtml;

            // Catatan
            const catatan = document.querySelector('#form-penerimaan textarea[name="catatan"]')?.value?.trim();
            const catatanWrapper = document.getElementById('modal-catatan-wrapper');
            if (catatan) {
                document.getElementById('modal-catatan-text').innerText = catatan;
                catatanWrapper.classList.remove('hidden');
            } else {
                catatanWrapper.classList.add('hidden');
            }

            // Show modal animation
            const modal = document.getElementById('modal-konfirmasi-penerimaan');
            const card = document.getElementById('modal-konfirmasi-card');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                card.classList.remove('scale-95');
                card.classList.add('scale-100');
            }, 10);
        }

        function closeModalKonfirmasi() {
            const modal = document.getElementById('modal-konfirmasi-penerimaan');
            const card = document.getElementById('modal-konfirmasi-card');
            if (!modal) return;
            modal.classList.add('opacity-0');
            card.classList.remove('scale-100');
            card.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }

        function submitFormPenerimaanFinal() {
            const btn = document.getElementById('btn-submit-final-modal');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');
                const span = btn.querySelector('span');
                if (span) span.innerText = 'Memproses...';
            }
            document.getElementById('form-penerimaan').submit();
        }

        window.bindPilihSiswaButtons = function() {
            document.querySelectorAll('.btn-pilih-siswa').forEach(btn => {
                if (btn.dataset.bound) return;
                btn.dataset.bound = "true";
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const noInduk = this.dataset.noInduk;
                    const originalText = this.innerText;
                    this.innerText = 'Memuat...';
                    this.classList.add('opacity-75');

                    fetch(`{{ route('penerimaan.catat') }}?no_induk=${encodeURIComponent(noInduk)}&ajax=1`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            this.innerText = originalText;
                            this.classList.remove('opacity-75');
                            return;
                        }

                        updateCatatSelectedStudentRows(noInduk);

                        const newUrl = `{{ route('penerimaan.catat') }}?no_induk=${encodeURIComponent(noInduk)}`;
                        window.history.pushState({ path: newUrl }, '', newUrl);

                        renderDetailSiswa(data);
                    })
                    .catch(err => {
                        this.innerText = originalText;
                        this.classList.remove('opacity-75');
                        console.error(err);
                        alert('Gagal memuat data siswa via AJAX.');
                    });
                });
            });
        };

        document.addEventListener('DOMContentLoaded', function() {
            const formPenerimaan = document.getElementById('form-penerimaan');
            if (formPenerimaan) {
                formPenerimaan.addEventListener('change', updateTotalBayar);
                formPenerimaan.addEventListener('input', updateTotalBayar);

                formPenerimaan.addEventListener('submit', function(e) {
                    const selectedItems = document.querySelectorAll('#form-penerimaan input[type="checkbox"]:checked');
                    const customSec = document.getElementById('section-custom-penerimaan');
                    let hasValidCustom = false;
                    if (customSec && !customSec.classList.contains('hidden')) {
                        document.querySelectorAll('.custom-item-row').forEach(row => {
                            const customNama = row.querySelector('.input-custom-nama')?.value?.trim();
                            const customNominal = parseInt(row.querySelector('.input-custom-nominal')?.value || 0, 10);
                            if (customNama && customNominal > 0) {
                                hasValidCustom = true;
                            }
                        });
                    }

                    if (selectedItems.length === 0 && !hasValidCustom) {
                        e.preventDefault();
                        alert('Silakan centang tagihan atau isi minimal 1 penerimaan tambahan.');
                        return false;
                    }
                });
            }

            const inputNominalTw = document.getElementById('input-nominal-tabungan-wajib');
            if (inputNominalTw) {
                inputNominalTw.addEventListener('input', updateTotalBayar);
                inputNominalTw.addEventListener('change', updateTotalBayar);
            }

            window.bindPilihSiswaButtons();

            // Direct click handlers for tab buttons
            document.addEventListener('click', function(e) {
                const sppBtn = e.target.closest('#tab-btn-spp');
                if (sppBtn) {
                    e.preventDefault();
                    switchTab('spp');
                }
                const twBtn = e.target.closest('#tab-btn-tabungan_wajib');
                if (twBtn) {
                    e.preventDefault();
                    switchTab('tabungan_wajib');
                }
                const iuranBtn = e.target.closest('#tab-btn-iuran');
                if (iuranBtn) {
                    e.preventDefault();
                    switchTab('iuran');
                }
            });

            @if(isset($siswa))
                switchTab('spp');
                updateTotalBayar();
            @endif
        });
    </script>
</x-layouts.app>
