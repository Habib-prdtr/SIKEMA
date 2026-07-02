<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreSiswaRequest;
use App\Http\Requests\Master\UpdateSiswaRequest;
use App\Models\Siswa;
use App\Services\MasterDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DataSiswaController extends Controller
{
    protected MasterDataService $masterDataService;

    public function __construct(MasterDataService $masterDataService)
    {
        $this->masterDataService = $masterDataService;
    }

    /**
     * Daftar siswa dengan pagination dan pencarian.
     */
    public function index(Request $request): View
    {
        $siswa = $this->masterDataService->getDaftarSiswa($request);

        return view('master.siswa.index', compact('siswa'));
    }

    /**
     * Form tambah siswa.
     */
    public function create(): View
    {
        return view('master.siswa.create');
    }

    /**
     * Cek apakah no induk sudah terdaftar (AJAX).
     */
    public function cekNoInduk(Request $request): \Illuminate\Http\JsonResponse
    {
        $noInduk = $request->query('no_induk');
        $ignoreId = $request->query('ignore_id');
        
        $exists = $this->masterDataService->cekNoIndukExist((string) $noInduk, $ignoreId ? (int) $ignoreId : null);
        
        return response()->json([
            'exists' => $exists
        ]);
    }

    /**
     * Simpan siswa baru.
     */
    public function store(StoreSiswaRequest $request): RedirectResponse
    {
        $this->masterDataService->simpanDataSiswa($request->validated());

        return redirect()->route('master.siswa.index')
            ->with('sukses', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Form edit siswa.
     */
    public function edit(Siswa $siswa): View
    {
        return view('master.siswa.edit', compact('siswa'));
    }

    /**
     * Update data siswa.
     */
    public function update(UpdateSiswaRequest $request, Siswa $siswa): RedirectResponse
    {
        $this->masterDataService->updateDataSiswa($siswa, $request->validated());

        return redirect()->route('master.siswa.index')
            ->with('sukses', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Hapus siswa (soft-check: cegah hapus jika punya transaksi).
     */
    public function destroy(Siswa $siswa): RedirectResponse
    {
        if ($this->masterDataService->cekSiswaPunyaTransaksi($siswa)) {
            return back()->withErrors([
                'error' => 'Siswa tidak dapat dihapus karena sudah memiliki data transaksi.',
            ]);
        }

        $this->masterDataService->hapusDataSiswa($siswa);

        return redirect()->route('master.siswa.index')
            ->with('sukses', 'Data siswa berhasil dihapus.');
    }

    /**
     * Tampilkan form import siswa.
     */
    public function showImportForm(): View
    {
        return view('master.siswa.import');
    }

    /**
     * Proses import data siswa dari file excel.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,xlsm,csv',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes' => 'Format file harus berupa xlsx, xls, xlsm, atau csv.',
        ]);

        $file = $request->file('file');
        
        try {
            $reader = IOFactory::createReaderForFile($file->getRealPath());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            
            $worksheet = $spreadsheet->getSheetByName('Siswa') ?: $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            $importedCount = $this->masterDataService->importDataSiswa($rows);
            
            return redirect()->route('master.siswa.index')
                ->with('sukses', "Berhasil mengimpor {$importedCount} data siswa.");
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal membaca atau memproses file Excel: ' . $e->getMessage()]);
        }
    }
}
