<?php

namespace App\Services;

use App\Models\Pengeluaran;
use App\Models\PosBiaya;
use App\Models\Sekolah;
use App\Models\TahunAjaran;
use App\Models\Transaksi;
use App\Utils\ExcelExportUtil;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use function Termwind\style;

class LaporanService
{
    public function getLaporanPenerimaan(Request $request, ?TahunAjaran $tahunAktif): array
    {
        $tahunList = TahunAjaran::orderByDesc('nama')->get();
        $tahunAjaranId = $request->get('tahun_ajaran_id', $tahunAktif?->id);

        $query = Transaksi::with([
            'siswaTahunAjaran.siswa',
            'details.jenisPenerimaan',
            'user',
        ])
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->orderByDesc('tanggal');

        // Filter bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        // Filter tahun kalender
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        // Filter jenjang kelas fokus
        $jenjang = \App\Models\Sekolah::getJenjangAktif();
        if ($jenjang !== 'semua' && in_array($jenjang, ['7', '8', '9'])) {
            $like = \Illuminate\Support\Facades\DB::getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->whereHas('siswaTahunAjaran.siswa', fn ($q) => $q->where('kelas', $like, "{$jenjang}%"));
        }

        // Filter siswa
        if ($request->filled('siswa_id')) {
            $query->whereHas('siswaTahunAjaran', fn ($q) => $q->where('siswa_id', $request->siswa_id));
        }

        $transaksi = $query->get();

        // Rekap total
        $totalPenerimaan = $transaksi->sum('total_bayar');

        // Rekap per jenis
        $totalSpp = $transaksi->flatMap(fn ($t) => $t->details)->where('jenis', 'spp')->sum('nominal');
        $totalIuran = $transaksi->flatMap(fn ($t) => $t->details)->where('jenis', 'iuran')->sum('nominal');
        $totalTunggakan = $transaksi->flatMap(fn ($t) => $t->details)->where('jenis', 'tunggakan')->sum('nominal');

        return compact(
            'transaksi',
            'tahunList',
            'tahunAktif',
            'totalPenerimaan',
            'totalSpp',
            'totalIuran',
            'totalTunggakan'
        );
    }

    public function getLaporanPengeluaran(Request $request, ?TahunAjaran $tahunAktif): array
    {
        $tahunList = TahunAjaran::orderByDesc('nama')->get();
        $tahunAjaranId = $request->get('tahun_ajaran_id', $tahunAktif?->id);

        $posList = PosBiaya::where('tahun_ajaran_id', $tahunAjaranId)
            ->orderBy('nama')
            ->get();

        $query = Pengeluaran::with(['posBiaya', 'user'])
            ->whereHas('posBiaya', fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->orderByDesc('tanggal');

        // Filter bulan
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        // Filter pos biaya
        if ($request->filled('pos_biaya_id')) {
            $query->where('pos_biaya_id', $request->pos_biaya_id);
        }

        $pengeluaran = $query->get();
        $totalPengeluaran = (int) $pengeluaran->sum('jumlah');

        // Rekap per pos biaya
        $rekapPerPos = PosBiaya::withSum('pengeluaran', 'jumlah')
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->when($request->filled('bulan'), function ($q) use ($request) {
                $q->whereHas('pengeluaran', fn ($p) => $p->where('bulan', $request->bulan));
            })
            ->orderBy('nama')
            ->get()
            ->map(function ($pos) {
                return (object) [
                    'nama' => $pos->nama,
                    'anggaran' => $pos->anggaran ?? 0,
                    'total' => (int) ($pos->pengeluaran_sum_jumlah ?? 0),
                ];
            })
            ->filter(fn ($r) => $r->total > 0);

        return compact(
            'pengeluaran',
            'tahunList',
            'posList',
            'tahunAktif',
            'totalPengeluaran',
            'rekapPerPos'
        );
    }

    public function exportPenerimaanToExcel(Request $request, ?TahunAjaran $tahunAktif): Spreadsheet
    {
        $data = $this->getLaporanPenerimaan($request, $tahunAktif);

        $tahunAjaranId = $request->get('tahun_ajaran_id', $tahunAktif?->id);
        $tahunAjaran = TahunAjaran::find($tahunAjaranId);

        $periode = ($request->filled('bulan') ? \Carbon\Carbon::create(null, $request->bulan)->locale('id')->isoFormat('MMMM') . ' ' : '') .
                   ($tahunAjaran?->nama ?? ($tahunAktif?->nama ?? '-'));

        return ExcelExportUtil::createMultiSheetReport([
            [
                'title' => 'Laporan Penerimaan',
                'periode' => $periode,
                'columns' => ['No', 'Tanggal', 'Siswa', 'Jenis', 'Nominal'],
                'data' => $data['transaksi'],
                'mapper' => function ($sheet, $t, $row, $index) {
                    $sheet->setCellValue('A' . $row, $index);
                    $sheet->setCellValue('B' . $row, format_tanggal($t->tanggal));
                    $sheet->setCellValue('C' . $row, $t->siswaTahunAjaran->siswa->nama);
                    $rincianStr = $t->details->map(function ($d) {
                        if ($d->jenis === 'spp') return "SPP Bln {$d->bulan}";
                        if ($d->jenis === 'tabungan_wajib') return 'Tabungan Wajib';
                        if ($d->jenis === 'iuran') return $d->jenisPenerimaan->nama ?? 'Iuran';
                        if ($d->jenis === 'tunggakan') return 'Cicilan Tunggakan';
                        return $d->keterangan ?? 'Custom';
                    })->join(', ');
                    $sheet->setCellValue('D' . $row, $rincianStr);
                    $sheet->setCellValue('E' . $row, $t->total_bayar);
                },
                'totalRow' => ['col' => 'E', 'label' => 'TOTAL PENERIMAAN', 'value' => $data['totalPenerimaan']]
            ]
        ]);
    }

    public function exportPengeluaranToExcel(Request $request, ?TahunAjaran $tahunAktif): Spreadsheet
    {
        $data = $this->getLaporanPengeluaran($request, $tahunAktif);

        $tahunAjaranId = $request->get('tahun_ajaran_id', $tahunAktif?->id);
        $tahunAjaran = TahunAjaran::find($tahunAjaranId);

        $periode = ($request->filled('bulan') ? \Carbon\Carbon::create(null, $request->bulan)->locale('id')->isoFormat('MMMM') . ' ' : '') .
                   ($tahunAjaran?->nama ?? ($tahunAktif?->nama ?? '-'));

        return ExcelExportUtil::createMultiSheetReport([
            [
                'title' => 'Rekap Pengeluaran per Pos',
                'periode' => $periode,
                'columns' => ['No', 'Pos Biaya', 'Anggaran', 'Total Terpakai'],
                'data' => $data['rekapPerPos'],
                'mapper' => function ($sheet, $r, $row, $index) {
                    $sheet->setCellValue('A' . $row, $index);
                    $sheet->setCellValue('B' . $row, $r->nama);
                    $sheet->setCellValue('C' . $row, $r->anggaran);
                    $sheet->setCellValue('D' . $row, $r->total);
                },
                'totalRow' => ['col' => 'D', 'label' => 'TOTAL', 'value' => $data['totalPengeluaran']]
            ],
            [
                'title' => 'Detail Pengeluaran',
                'periode' => $periode,
                'columns' => ['No', 'Tanggal', 'Pos Biaya', 'Keterangan', 'Nominal'],
                'data' => $data['pengeluaran'],
                'mapper' => function ($sheet, $p, $row, $index) {
                    $sheet->setCellValue('A' . $row, $index);
                    $sheet->setCellValue('B' . $row, format_tanggal($p->tanggal));
                    $sheet->setCellValue('C' . $row, $p->posBiaya->nama);
                    $sheet->setCellValue('D' . $row, $p->keterangan);
                    $sheet->setCellValue('E' . $row, $p->jumlah);
                },
                'totalRow' => ['col' => 'E', 'label' => 'TOTAL', 'value' => $data['totalPengeluaran']]
            ]
        ]);
    }

}
