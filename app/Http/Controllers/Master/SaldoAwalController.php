<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreSaldoAwalRequest;
use App\Http\Requests\Master\UpdateSaldoAwalRequest;
use App\Models\Pengeluaran;
use App\Models\PosBiaya;
use App\Models\SaldoAwal;
use App\Models\TahunAjaran;
use App\Models\Transaksi;
use App\Services\MasterDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\View\View;

class SaldoAwalController extends Controller
{
    protected MasterDataService $masterDataService;

    public function __construct(MasterDataService $masterDataService)
    {
        $this->masterDataService = $masterDataService;
    }

    /**
     * Tampilkan Dashboard Saldo Kas dan Rekor Transaksi.
     */
    public function index(): View
    {
        $tahunAktif = TahunAjaran::aktif();
        if (!$tahunAktif) {
            return view('master.saldo-awal.index', [
                'tahunAktif' => null,
                'saldoAwal' => null,
            ]);
        }

        $saldoAwal = $this->masterDataService->getSaldoAwalTahunAktif($tahunAktif);

        if (!$saldoAwal) {
            return view('master.saldo-awal.index', [
                'tahunAktif' => $tahunAktif,
                'saldoAwal' => null,
            ]);
        }

        // Fetch Pos Biaya with budget (anggaran)
        $posBiayaList = PosBiaya::where('tahun_ajaran_id', $tahunAktif->id)
            ->where('anggaran', '>', 0)
            ->get();

        // Fetch income (pemasukan from Transaksi)
        $pemasukanList = Transaksi::where('tahun_ajaran_id', $tahunAktif->id)
            ->with('siswaTahunAjaran.siswa')->get();

        // Fetch expenses (pengeluaran)
        $pengeluaranList = Pengeluaran::whereHas('posBiaya', function ($q) use ($tahunAktif) {
            $q->where('tahun_ajaran_id', $tahunAktif->id);
        })->with('posBiaya')->get();

        $totalAnggaran = $posBiayaList->sum('anggaran');
        $totalPemasukan = $pemasukanList->sum('total_bayar');
        $totalPengeluaran = $pengeluaranList->sum('jumlah');
        $saldoSaatIni = $saldoAwal->jumlah + $totalAnggaran + $totalPemasukan - $totalPengeluaran;

        // Compile chronological record log
        $records = collect();

        // 1. Initial Balance Record
        $records->push((object)[
            'tanggal' => $saldoAwal->created_at ?? $tahunAktif->created_at ?? now(),
            'jenis' => 'saldo_awal',
            'kategori' => 'Saldo Awal',
            'keterangan' => $saldoAwal->keterangan ?: 'Saldo awal kas untuk tahun ajaran ' . $tahunAktif->nama,
            'debit' => $saldoAwal->jumlah,
            'kredit' => 0,
            'running_saldo' => 0,
        ]);

        // 2. Pos Biaya Anggaran Records
        foreach ($posBiayaList as $pos) {
            $records->push((object)[
                'tanggal' => $pos->created_at ?? $tahunAktif->created_at ?? now(),
                'jenis' => 'anggaran',
                'kategori' => 'Anggaran Pos',
                'keterangan' => 'Alokasi anggaran pos biaya: ' . $pos->nama . ($pos->keterangan ? ' - ' . $pos->keterangan : ''),
                'debit' => $pos->anggaran,
                'kredit' => 0,
                'running_saldo' => 0,
            ]);
        }

        // 3. Income Records
        foreach ($pemasukanList as $t) {
            $records->push((object)[
                'tanggal' => $t->tanggal,
                'jenis' => 'pemasukan',
                'kategori' => 'Pemasukan (SPP/Iuran)',
                'keterangan' => 'Pembayaran oleh ' . ($t->siswaTahunAjaran->siswa->nama ?? 'Siswa') . ' (' . ($t->siswaTahunAjaran->siswa->kelas ?? '-') . ')' . ($t->keterangan ? ' - ' . $t->keterangan : ''),
                'debit' => $t->total_bayar,
                'kredit' => 0,
                'running_saldo' => 0,
            ]);
        }

        // 4. Expense Records
        foreach ($pengeluaranList as $p) {
            $records->push((object)[
                'tanggal' => $p->tanggal,
                'jenis' => 'pengeluaran',
                'kategori' => 'Pengeluaran',
                'keterangan' => 'Realisasi anggaran: ' . ($p->posBiaya->nama ?? '-') . ($p->keterangan ? ' - ' . $p->keterangan : ''),
                'debit' => 0,
                'kredit' => $p->jumlah,
                'running_saldo' => 0,
            ]);
        }

        // Sort ascending to calculate running balance chronologically
        $records = $records->sortBy(function ($item) {
            return $item->tanggal instanceof \Carbon\Carbon ? $item->tanggal->timestamp : strtotime($item->tanggal);
        });

        $running = 0;
        foreach ($records as $r) {
            $running += $r->debit - $r->kredit;
            $r->running_saldo = $running;
        }

        // Sort descending for display (newest first)
        $records = $records->sortByDesc(function ($item) {
            return $item->tanggal instanceof \Carbon\Carbon ? $item->tanggal->timestamp : strtotime($item->tanggal);
        })->values();

        // Paginate records
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $perPage = 10;
        $currentPageItems = $records->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedRecords = new LengthAwarePaginator(
            $currentPageItems,
            $records->count(),
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );

        return view('master.saldo-awal.index', compact(
            'tahunAktif',
            'saldoAwal',
            'totalAnggaran',
            'totalPemasukan',
            'totalPengeluaran',
            'saldoSaatIni',
            'paginatedRecords'
        ));
    }

    /**
     * Simpan saldo awal untuk tahun ajaran yang belum ada saldo.
     */
    public function store(StoreSaldoAwalRequest $request): RedirectResponse
    {
        $this->masterDataService->simpanSaldoAwal($request->validated());

        return redirect()->route('master.saldo-awal.index')
            ->with('sukses', 'Saldo kas berhasil diinisialisasi.');
    }

    /**
     * Update saldo awal yang sudah ada.
     */
    public function update(UpdateSaldoAwalRequest $request, SaldoAwal $saldoAwal): RedirectResponse
    {
        $this->masterDataService->updateSaldoAwal($saldoAwal, $request->validated());

        return redirect()->route('master.saldo-awal.index')
            ->with('sukses', 'Saldo awal kas berhasil diperbarui.');
    }
}
