<x-layouts.app title="Laporan Rekapitulasi">
    <x-slot:pageTitle>Laporan / Rekapitulasi</x-slot:pageTitle>

    {{-- Print CSS --}}
    <style>
        @media print {
            aside, header, nav, .no-print, button, form {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .print-only {
                display: block !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            th, td {
                border: 1px solid #ccc !important;
                padding: 6px 8px !important;
                font-size: 11px !important;
            }
        }
        .print-only {
            display: none;
        }
    </style>

    <div class="space-y-5">
        {{-- Header Halaman --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Rekapitulasi Pembayaran</h1>
                <p class="text-gray-500 text-sm mt-0.5">Laporan rekap pembayaran SPP, iuran, dan gabungan</p>
            </div>
            
            {{-- Tahun Ajaran Selector --}}
            <form method="GET" action="{{ route('laporan.rekapitulasi') }}" id="form-tahun">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <select name="tahun_ajaran_id" onchange="this.form.submit()" class="form-select w-full md:w-56 font-semibold">
                    @foreach($tahunList as $thn)
                        <option value="{{ $thn->id }}" {{ $selectedTahunId == $thn->id ? 'selected' : '' }}>
                            Tahun Ajaran: {{ $thn->nama }} {{ $thn->is_aktif ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- Tab Navigation --}}
        <div class="flex border-b border-gray-200 no-print">
            <a href="{{ route('laporan.rekapitulasi', array_merge(request()->except('page'), ['tab' => 'spp'])) }}" 
               class="py-3 px-6 font-medium text-sm border-b-2 transition-all duration-150 {{ $tab === 'spp' ? 'border-emerald-600 text-emerald-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Laporan Pembayaran SPP
            </a>
            <a href="{{ route('laporan.rekapitulasi', array_merge(request()->except('page'), ['tab' => 'iuran'])) }}" 
               class="py-3 px-6 font-medium text-sm border-b-2 transition-all duration-150 {{ $tab === 'iuran' ? 'border-emerald-600 text-emerald-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Laporan Pembayaran Iuran
            </a>
            <a href="{{ route('laporan.rekapitulasi', array_merge(request()->except('page'), ['tab' => 'gabungan'])) }}" 
               class="py-3 px-6 font-medium text-sm border-b-2 transition-all duration-150 {{ $tab === 'gabungan' ? 'border-emerald-600 text-emerald-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Rekap Gabungan
            </a>
        </div>

        {{-- Filter & Action Bar --}}
        <div class="card p-4 space-y-4 no-print">
            <form method="GET" action="{{ route('laporan.rekapitulasi') }}" class="flex flex-col md:flex-row flex-wrap items-center gap-3">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="hidden" name="tahun_ajaran_id" value="{{ $selectedTahunId }}">

                {{-- Cari --}}
                <div class="w-full md:w-60">
                    <input type="text" name="cari" value="{{ $cari }}" placeholder="Nama / No. Induk..." class="form-input w-full">
                </div>

                {{-- Kelas --}}
                <div class="w-full md:w-36">
                    <select name="kelas" class="form-select w-full">
                        <option value="">Semua Kelas</option>
                        <option value="7" {{ $kelasFilter === '7' ? 'selected' : '' }}>Tingkat 7</option>
                        <option value="8" {{ $kelasFilter === '8' ? 'selected' : '' }}>Tingkat 8</option>
                        <option value="9" {{ $kelasFilter === '9' ? 'selected' : '' }}>Tingkat 9</option>
                        <hr>
                        @foreach($daftarKelas as $kls)
                            <option value="{{ $kls }}" {{ $kelasFilter === $kls ? 'selected' : '' }}>Kelas {{ $kls }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- SPP Bulan Filter --}}
                @if($tab === 'spp')
                    <div class="w-full md:w-40">
                        <select name="bulan" class="form-select w-full">
                            <option value="">Semua Bulan (YTD)</option>
                            @php
                                $months = [
                                    7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                                    10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                                    4 => 'April', 5 => 'Mei', 6 => 'Juni'
                                ];
                            @endphp
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}" {{ $bulanFilter == $num ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Iuran Jenis Filter --}}
                @if($tab === 'iuran')
                    <div class="w-full md:w-48">
                        <select name="jenis_penerimaan_id" class="form-select w-full">
                            <option value="">Semua Iuran</option>
                            @foreach($jenisPenerimaanList as $jp)
                                <option value="{{ $jp->id }}" {{ $iuranFilterId == $jp->id ? 'selected' : '' }}>{{ $jp->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="flex items-center gap-2 w-full md:w-auto ml-auto">
                    <button type="submit" class="btn-primary flex-1 md:flex-none">Filter</button>
                    @if($cari || $kelasFilter || $bulanFilter || $iuranFilterId)
                        <a href="{{ route('laporan.rekapitulasi', ['tab' => $tab, 'tahun_ajaran_id' => $selectedTahunId]) }}" class="btn-secondary">Reset</a>
                    @endif
                </div>
            </form>

            <div class="flex flex-wrap items-center justify-between border-t border-gray-150 pt-3 gap-2">
                <p class="text-xs text-gray-500">
                    Menampilkan {{ $students instanceof \Illuminate\Pagination\LengthAwarePaginator ? $students->firstItem() : 1 }} - {{ $students instanceof \Illuminate\Pagination\LengthAwarePaginator ? $students->lastItem() : $students->count() }} data siswa
                </p>
                <div class="flex gap-2">
                    <a href="{{ route('laporan.rekapitulasi.export', request()->all()) }}" class="btn-secondary btn-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Ekspor Excel
                    </a>
                    <button onclick="window.print()" class="btn-secondary btn-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Cetak PDF
                    </button>
                </div>
            </div>
        </div>

        {{-- Print Only Header --}}
        <div class="print-only mb-6 text-center space-y-1">
            <h1 class="text-xl font-bold uppercase">Laporan Rekapitulasi Pembayaran</h1>
            <p class="text-sm font-semibold">Tahun Ajaran: {{ $tahunFilter?->nama ?? '-' }} | Jenis Laporan: {{ strtoupper($tab) }}</p>
            <hr class="border-black my-2">
        </div>

        {{-- Main Data Card --}}
        <div class="card">
            @if($students->isEmpty())
                <div class="p-12 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm">Tidak ditemukan data rekapitulasi.</p>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="table min-w-full">
                        <thead>
                            @if($tab === 'spp')
                                <tr>
                                    <th class="w-12 text-center">No</th>
                                    <th>No. Induk</th>
                                    <th>Nama Siswa</th>
                                    <th class="text-center">Kelas</th>
                                    <th class="text-right">Total Tagihan</th>
                                    <th class="text-right">Total Terbayar</th>
                                    <th class="text-right">Sisa Tagihan</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            @elseif($tab === 'iuran')
                                <tr>
                                    <th class="w-12 text-center">No</th>
                                    <th>No. Induk</th>
                                    <th>Nama Siswa</th>
                                    <th class="text-center">Kelas</th>
                                    <th>Nama Iuran</th>
                                    <th class="text-right">Tagihan</th>
                                    <th class="text-right">Terbayar</th>
                                    <th class="text-right">Sisa</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            @else {{-- gabungan --}}
                                @php
                                    $numIur = count($jenisPenerimaanList);
                                @endphp
                                <tr>
                                    <th class="text-center" rowspan="2">Kelas</th>
                                    <th class="text-center" rowspan="2">No. Induk</th>
                                    <th rowspan="2">Nama Siswa</th>
                                    <th class="text-right" rowspan="2">Tagihan Setahun</th>
                                    <th class="text-center" colspan="{{ 2 + $numIur }}">Rincian Yang Sudah Dibayar</th>
                                    <th class="text-right" rowspan="2">Total Sudah Bayar</th>
                                    <th class="text-right" rowspan="2">Total Kurang Bayar</th>
                                </tr>
                                <tr>
                                    <th class="text-right">SPP</th>
                                    @foreach($jenisPenerimaanList as $jp)
                                        <th class="text-right">{{ $jp->nama }}</th>
                                    @endforeach
                                    <th class="text-right">Tunggakan</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                            @php
                                $sumTagihanSetahun = 0;
                                $sumSppTerbayar = 0;
                                $sumIurTerbayarMap = [];
                                foreach($jenisPenerimaanList as $jp) { $sumIurTerbayarMap[$jp->id] = 0; }
                                $sumTunggakanTerbayar = 0;
                                $sumTotalSudahBayar = 0;
                                $sumTotalKurangBayar = 0;

                                $sumTagihanSpp = 0;
                                $sumTerbayarSpp = 0;
                                $sumTagihanIur = 0;
                                $sumTerbayarIur = 0;
                                $counter = 1;
                            @endphp
                            @foreach($students as $index => $sta)
                                @if($tab === 'spp')
                                    @php
                                        if ($bulanFilter) {
                                            $bill = $sta->tagihanSpp->where('bulan', $bulanFilter)->first();
                                            $tagihan = $bill ? $bill->tagihan : 0;
                                            $terbayar = $bill ? $bill->terbayar : 0;
                                            $status = $bill ? $bill->status : 'belum';
                                        } else {
                                            $tagihan = $sta->tagihanSpp->sum('tagihan');
                                            $terbayar = $sta->tagihanSpp->sum('terbayar');
                                            $sisa = $tagihan - $terbayar;
                                            $status = $sisa <= 0 ? 'lunas' : ($terbayar > 0 ? 'cicilan' : 'belum');
                                        }
                                        $sisa = $tagihan - $terbayar;
                                        
                                        $sumTagihanSpp += $tagihan;
                                        $sumTerbayarSpp += $terbayar;
                                    @endphp
                                    <tr>
                                        <td class="text-center text-xs text-gray-500">{{ $index + 1 }}</td>
                                        <td class="font-mono text-xs">{{ $sta->siswa->no_induk }}</td>
                                        <td class="font-medium text-gray-900">{{ $sta->siswa->nama }}</td>
                                        <td class="text-center font-semibold text-gray-700">{{ $sta->siswa->kelas }}</td>
                                        <td class="text-right">{{ format_rupiah($tagihan) }}</td>
                                        <td class="text-right text-emerald-600 font-medium">{{ format_rupiah($terbayar) }}</td>
                                        <td class="text-right text-red-600 font-semibold">{{ format_rupiah($sisa) }}</td>
                                        <td class="text-center">
                                            @if($status === 'lunas')
                                                <span class="badge-green">Lunas</span>
                                            @elseif($status === 'cicilan')
                                                <span class="badge-blue">Cicilan</span>
                                            @else
                                                <span class="badge-red">Belum Bayar</span>
                                            @endif
                                        </td>
                                    </tr>
                                @elseif($tab === 'iuran')
                                    @php
                                        $iurans = $sta->tagihanIuran;
                                        if ($iuranFilterId) {
                                            $iurans = $iurans->where('jenis_penerimaan_id', $iuranFilterId);
                                        }
                                    @endphp
                                    @foreach($iurans as $iur)
                                        @php
                                            $sisa = $iur->tagihan - $iur->terbayar;
                                            $sumTagihanIur += $iur->tagihan;
                                            $sumTerbayarIur += $iur->terbayar;
                                        @endphp
                                        <tr>
                                            <td class="text-center text-xs text-gray-500">{{ $counter++ }}</td>
                                            <td class="font-mono text-xs">{{ $sta->siswa->no_induk }}</td>
                                            <td class="font-medium text-gray-900">{{ $sta->siswa->nama }}</td>
                                            <td class="text-center font-semibold text-gray-700">{{ $sta->siswa->kelas }}</td>
                                            <td class="text-gray-800 text-sm">{{ $iur->jenisPenerimaan->nama ?? '-' }}</td>
                                            <td class="text-right">{{ format_rupiah($iur->tagihan) }}</td>
                                            <td class="text-right text-emerald-600 font-medium">{{ format_rupiah($iur->terbayar) }}</td>
                                            <td class="text-right text-red-600 font-semibold">{{ format_rupiah($sisa) }}</td>
                                            <td class="text-center">
                                                @if($iur->status === 'lunas')
                                                    <span class="badge-green">Lunas</span>
                                                @elseif($iur->status === 'cicilan')
                                                    <span class="badge-blue">Cicilan</span>
                                                @else
                                                    <span class="badge-red">Belum Bayar</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else {{-- gabungan --}}
                                    @php
                                        $sppTagihan = $sta->tagihanSpp->sum('tagihan');
                                        $sppTerbayar = $sta->tagihanSpp->sum('terbayar');
                                        $iurTagihan = $sta->tagihanIuran->sum('tagihan');
                                        $iurTerbayar = $sta->tagihanIuran->sum('terbayar');
                                        $tunggakanAwal = $sta->tunggakan_awal ?? 0;
                                        $tunggakanTerbayar = (int) ($tunggakanMap[$sta->id] ?? 0);

                                        $tagihanSetahun = $sppTagihan + $iurTagihan + $tunggakanAwal;
                                        $totalSudahBayar = $sppTerbayar + $iurTerbayar + $tunggakanTerbayar;
                                        $totalKurangBayar = max(0, $tagihanSetahun - $totalSudahBayar);

                                        $sumTagihanSetahun += $tagihanSetahun;
                                        $sumSppTerbayar += $sppTerbayar;
                                        $sumTunggakanTerbayar += $tunggakanTerbayar;
                                        $sumTotalSudahBayar += $totalSudahBayar;
                                        $sumTotalKurangBayar += $totalKurangBayar;
                                    @endphp
                                    <tr>
                                        <td class="text-center font-semibold text-gray-700 text-xs">{{ $sta->siswa->kelas }}</td>
                                        <td class="font-mono text-xs">{{ $sta->siswa->no_induk }}</td>
                                        <td class="font-medium text-gray-900 text-xs">{{ $sta->siswa->nama }}</td>
                                        <td class="text-right font-medium text-xs">{{ format_rupiah($tagihanSetahun) }}</td>
                                        <td class="text-right text-emerald-600 text-xs">{{ format_rupiah($sppTerbayar) }}</td>
                                        @foreach($jenisPenerimaanList as $jp)
                                            @php
                                                $byr = $sta->tagihanIuran->where('jenis_penerimaan_id', $jp->id)->sum('terbayar');
                                                $sumIurTerbayarMap[$jp->id] += $byr;
                                            @endphp
                                            <td class="text-right text-emerald-600 text-xs">{{ format_rupiah($byr) }}</td>
                                        @endforeach
                                        <td class="text-right text-emerald-600 text-xs">{{ format_rupiah($tunggakanTerbayar) }}</td>
                                        <td class="text-right text-emerald-700 font-semibold text-xs">{{ format_rupiah($totalSudahBayar) }}</td>
                                        <td class="text-right text-red-600 font-bold text-xs">{{ format_rupiah($totalKurangBayar) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            @if($tab === 'spp')
                                <tr class="bg-gray-50 font-bold border-t-2 border-gray-200">
                                    <td colspan="4" class="text-right">TOTAL REKAPITULASI:</td>
                                    <td class="text-right">{{ format_rupiah($sumTagihanSpp) }}</td>
                                    <td class="text-right text-emerald-600">{{ format_rupiah($sumTerbayarSpp) }}</td>
                                    <td class="text-right text-red-600">{{ format_rupiah($sumTagihanSpp - $sumTerbayarSpp) }}</td>
                                    <td></td>
                                </tr>
                            @elseif($tab === 'iuran')
                                <tr class="bg-gray-50 font-bold border-t-2 border-gray-200">
                                    <td colspan="5" class="text-right">TOTAL REKAPITULASI:</td>
                                    <td class="text-right">{{ format_rupiah($sumTagihanIur) }}</td>
                                    <td class="text-right text-emerald-600">{{ format_rupiah($sumTerbayarIur) }}</td>
                                    <td class="text-right text-red-600">{{ format_rupiah($sumTagihanIur - $sumTerbayarIur) }}</td>
                                    <td></td>
                                </tr>
                            @else
                                <tr class="bg-gray-50 font-bold border-t-2 border-gray-200 text-xs">
                                    <td colspan="3" class="text-right text-sm">Grand Total</td>
                                    <td class="text-right text-sm">{{ format_rupiah($sumTagihanSetahun) }}</td>
                                    <td class="text-right text-emerald-600 text-xs">{{ format_rupiah($sumSppTerbayar) }}</td>
                                    @foreach($jenisPenerimaanList as $jp)
                                        <td class="text-right text-emerald-600 text-xs">{{ format_rupiah($sumIurTerbayarMap[$jp->id] ?? 0) }}</td>
                                    @endforeach
                                    <td class="text-right text-emerald-600 text-xs">{{ format_rupiah($sumTunggakanTerbayar) }}</td>
                                    <td class="text-right text-emerald-700 text-sm">{{ format_rupiah($sumTotalSudahBayar) }}</td>
                                    <td class="text-right text-red-600 text-sm">{{ format_rupiah($sumTotalKurangBayar) }}</td>
                                </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>

                {{-- Pagination Links --}}
                @if($students instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="p-4 no-print border-t border-gray-200">
                        {{ $students->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>
