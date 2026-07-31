<x-layouts.app title="Detail Transaksi">
    <x-slot:pageTitle>Penerimaan / Detail Transaksi</x-slot:pageTitle>

    <style>
        @media print {
            @page {
                size: 95mm 140mm;
                margin: 0;
            }
            html, body {
                width: 95mm !important;
                height: 140mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                overflow: hidden !important;
            }
            aside, header, nav, #sidebar-overlay, .no-print, [role="navigation"] {
                display: none !important;
            }
            main {
                padding: 0 !important;
                margin: 0 !important;
                overflow: visible !important;
            }
            body * {
                visibility: hidden !important;
            }
            #kwitansi, #kwitansi * {
                visibility: visible !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            #kwitansi {
                position: fixed !important;
                left: 3mm !important;
                top: 6mm !important;
                width: 89mm !important;
                height: 130mm !important;
                margin: 0 !important;
                padding: 10px 12px !important;
                border: 1.5px solid #000000 !important;
                border-radius: 0px !important;
                box-shadow: none !important;
                background: #ffffff !important;
                box-sizing: border-box !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: flex-start !important;
            }
        }
    </style>

    {{-- Tombol aksi (no-print) --}}
    <div class="flex items-center gap-3 mb-5 no-print">
        <a href="{{ route('penerimaan.index') }}" class="btn-secondary btn-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
        <button id="btn-print" class="btn-primary btn-sm" onclick="window.print()">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak Kwitansi (9.5x14 cm Portrait)
        </button>
    </div>

    {{-- Kwitansi (9.5cm x 14cm Format Portrait) --}}
    <div class="card max-w-[95mm] mx-auto border-2 border-black p-3 text-black font-bold flex flex-col justify-start min-h-[134mm]" id="kwitansi">

        {{-- Kop --}}
        <div class="pb-2 border-b border-black text-center shrink-0">
            <h2 style="font-size: 8.5px; line-height: 1.2;" class="font-black text-black uppercase tracking-wide">{{ $sekolah->nama_sekolah ?? 'MTS IHYAUL ULUM' }}</h2>
            @if($sekolah->nama_yayasan ?? false)
                <p style="font-size: 8.5px; line-height: 1.2;" class="font-bold text-black mt-0.5">{{ $sekolah->nama_yayasan }}</p>
            @endif
            @if(($sekolah->alamat ?? false) || ($sekolah->telepon ?? false))
                <p style="font-size: 8.5px; line-height: 1.2;" class="font-bold text-black mt-0.5">
                    {{ $sekolah->alamat }}
                    @if(($sekolah->alamat ?? false) && ($sekolah->telepon ?? false)) | @endif
                    @if($sekolah->telepon ?? false) Telp: {{ $sekolah->telepon }} @endif
                </p>
            @endif
            <div class="mt-1.5 inline-block border border-black text-black px-2 py-0.5 rounded text-[9px] font-black tracking-widest uppercase">
                BUKTI PEMBAYARAN / KWITANSI
            </div>
        </div>

        {{-- No Transaksi & Tanggal --}}
        <div class="flex justify-between items-center text-[10px] font-bold border-b border-black py-1.5 shrink-0">
            <div>
                <span class="text-black">No. Trx:</span>
                <span class="font-mono text-xs font-black text-black ml-1">{{ $transaksi->no_transaksi }}</span>
            </div>
            <div class="text-right">
                <span class="text-black">Tgl:</span>
                <span class="font-black text-black ml-1">{{ $transaksi->tanggal->locale('id')->isoFormat('D MMMM YYYY') }}</span>
            </div>
        </div>

        {{-- Data Siswa --}}
        <div class="border border-black rounded p-2 bg-gray-50/50 my-2 shrink-0 text-[10px] font-bold space-y-1">
            <div class="flex items-baseline justify-between gap-2">
                <div class="truncate"><span class="text-black">Nama:</span> <span class="font-black text-black ml-1">{{ $transaksi->siswaTahunAjaran->siswa->nama }}</span></div>
                <div class="shrink-0 text-right"><span class="text-black">No. Induk:</span> <span class="font-mono font-black text-black ml-1">{{ $transaksi->siswaTahunAjaran->siswa->no_induk }}</span></div>
            </div>
            <div class="flex items-baseline justify-between gap-2">
                <div><span class="text-black">Kelas:</span> <span class="font-black text-black ml-1">{{ $transaksi->siswaTahunAjaran->siswa->kelas }}</span></div>
                <div class="shrink-0 text-right"><span class="text-black">TA:</span> <span class="font-black text-black ml-1">{{ $transaksi->siswaTahunAjaran->tahunAjaran->nama }}</span></div>
            </div>
        </div>

        @php
            $rincianPembayaran = collect();
            $tarifSpp = $transaksi->siswaTahunAjaran->tarif_spp ?? 0;
            $namaDispensasi = $transaksi->siswaTahunAjaran->dispensasi->nama ?? 'Potongan';

            $processGroupItem = function ($group, $tarifSpp, $namaDispensasi) {
                $first = $group[0];
                $last = end($group);
                $totalNominal = collect($group)->sum('nominal');

                $firstBulanStr = \Carbon\Carbon::createFromDate($first->tahun, $first->bulan, 1)->locale('id')->isoFormat('MMMM');

                if (count($group) === 1) {
                    $keterangan = 'SPP ' . \Carbon\Carbon::createFromDate($first->tahun, $first->bulan, 1)->locale('id')->isoFormat('MMMM YYYY');
                } else {
                    $lastBulanStr = \Carbon\Carbon::createFromDate($last->tahun, $last->bulan, 1)->locale('id')->isoFormat('MMMM');
                    if ($first->tahun === $last->tahun) {
                        $keterangan = "SPP {$firstBulanStr} - {$lastBulanStr} {$first->tahun}";
                    } else {
                        $keterangan = "SPP {$firstBulanStr} {$first->tahun} - {$lastBulanStr} {$last->tahun}";
                    }
                }

                $hasDisp = false;
                foreach ($group as $item) {
                    if ($tarifSpp > 0 && $item->nominal < $tarifSpp) {
                        $hasDisp = true;
                        break;
                    }
                }

                if ($hasDisp) {
                    $keterangan .= " (Disp: {$namaDispensasi})";
                }

                return [
                    'keterangan' => $keterangan,
                    'nominal' => $totalNominal,
                ];
            };

            $sppDetails = $transaksi->details->where('jenis', 'spp')->sortBy(function ($item) {
                return ($item->tahun * 12) + $item->bulan;
            })->values();

            if ($sppDetails->isNotEmpty()) {
                $currentGroup = [];
                foreach ($sppDetails as $detail) {
                    if (empty($currentGroup)) {
                        $currentGroup[] = $detail;
                    } else {
                        $prev = end($currentGroup);
                        $prevIndex = ($prev->tahun * 12) + $prev->bulan;
                        $currIndex = ($detail->tahun * 12) + $detail->bulan;

                        if ($currIndex === $prevIndex + 1) {
                            $currentGroup[] = $detail;
                        } else {
                            $rincianPembayaran->push($processGroupItem($currentGroup, $tarifSpp, $namaDispensasi));
                            $currentGroup = [$detail];
                        }
                    }
                }
                if (!empty($currentGroup)) {
                    $rincianPembayaran->push($processGroupItem($currentGroup, $tarifSpp, $namaDispensasi));
                }
            }

            $nonSppDetails = $transaksi->details->where('jenis', '!=', 'spp');
            foreach ($nonSppDetails as $detail) {
                if ($detail->jenis === 'iuran') {
                    $label = $detail->jenisPenerimaan->nama ?? 'Iuran';
                } elseif ($detail->jenis === 'tabungan_wajib') {
                    $label = 'Tabungan Wajib';
                } elseif ($detail->jenis === 'custom') {
                    $label = $detail->keterangan ?? 'Penerimaan Lain';
                } else {
                    $label = 'Cicilan Tunggakan';
                }
                $rincianPembayaran->push([
                    'keterangan' => $label,
                    'nominal' => $detail->nominal,
                ]);
            }
        @endphp

        {{-- Rincian Pembayaran --}}
        <table class="w-full text-[10px] font-bold my-1 shrink-0">
            <thead>
                <tr class="border-b border-black">
                    <th class="text-left py-1 text-black font-black">Keterangan</th>
                    <th class="text-right py-1 text-black font-black">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-300 border-b border-black">
                @foreach($rincianPembayaran as $item)
                    <tr>
                        <td class="py-1 text-black font-bold">{{ $item['keterangan'] }}</td>
                        <td class="py-1 text-right font-black text-black">{{ format_rupiah($item['nominal']) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="pt-2 font-black text-black text-xs">TOTAL BAYAR</td>
                    <td class="pt-2 text-right font-black text-black text-xs">
                        {{ format_rupiah($transaksi->total_bayar) }}
                    </td>
                </tr>
            </tfoot>
        </table>

        @if($transaksi->catatan)
            <p class="text-[9px] font-bold text-black italic mt-1 shrink-0">Catatan: {{ $transaksi->catatan }}</p>
        @endif

        {{-- Tanda tangan --}}
        <div class="flex justify-end items-end pt-2 mt-auto shrink-0">
            <div class="text-center">
                <p style="font-size: 9px; line-height: 1.2;" class="text-black font-semibold">Kepala TU / Bendahara</p>
                <div class="h-7"></div>
                <p style="font-size: 9.5px; line-height: 1.2;" class="font-bold text-black border-t border-black pt-0.5 min-w-24">
                    {{ !empty($sekolah->kepala_tu) ? $sekolah->kepala_tu : $transaksi->user->name }}
                </p>
            </div>
        </div>
    </div>

</x-layouts.app>
