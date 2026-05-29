<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreSaldoAwalRequest;
use App\Http\Requests\Master\UpdateSaldoAwalRequest;
use App\Models\SaldoAwal;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SaldoAwalController extends Controller
{
    /**
     * Tampilkan saldo awal per tahun ajaran.
     */
    public function index(): View
    {
        $saldoList   = SaldoAwal::with('tahunAjaran')->orderByDesc('id')->get();
        $tahunList   = TahunAjaran::orderByDesc('nama')->get();

        return view('master.saldo-awal.index', compact('saldoList', 'tahunList'));
    }

    /**
     * Simpan saldo awal untuk tahun ajaran yang belum ada saldo.
     */
    public function store(StoreSaldoAwalRequest $request): RedirectResponse
    {
        SaldoAwal::create($request->validated());

        return redirect()->route('master.saldo-awal.index')
            ->with('sukses', 'Saldo awal berhasil disimpan.');
    }

    /**
     * Update saldo awal yang sudah ada.
     */
    public function update(UpdateSaldoAwalRequest $request, SaldoAwal $saldoAwal): RedirectResponse
    {
        $saldoAwal->update($request->validated());

        return redirect()->route('master.saldo-awal.index')
            ->with('sukses', 'Saldo awal berhasil diperbarui.');
    }
}
