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
use Illuminate\Support\Facades\DB;

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
     * Simpan siswa baru.
     */
    public function store(StoreSiswaRequest $request): RedirectResponse
    {
        Siswa::create($request->validated());

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
        $siswa->update($request->validated());

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

        // Cascade delete: siswa_tahun_ajaran → tagihan_spp/iuran akan ikut terhapus
        $siswa->delete();
 
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
            
            if (count($rows) <= 1) {
                return back()->withErrors(['error' => 'File Excel kosong atau tidak memiliki baris data.']);
            }
            
            $headers = array_map(function($h) {
                return strtolower(trim((string)$h));
            }, $rows[0]);
            
            $colIndex = [
                'no_induk' => -1,
                'nama' => -1,
                'kelas' => -1,
                'asrama' => -1,
                'jenis_kelamin' => -1,
                'tanggal_masuk' => -1,
            ];
            
            foreach ($headers as $index => $header) {
                if (str_contains($header, 'no_induk') || $header === 'nis' || $header === 'nisn' || str_contains($header, 'induk') || str_contains($header, 'nomor induk')) {
                    $colIndex['no_induk'] = $index;
                } elseif (str_contains($header, 'nama') || str_contains($header, 'siswa')) {
                    $colIndex['nama'] = $index;
                } elseif (str_contains($header, 'kelas')) {
                    $colIndex['kelas'] = $index;
                } elseif (str_contains($header, 'asrama')) {
                    $colIndex['asrama'] = $index;
                } elseif (str_contains($header, 'jenis kelamin') || str_contains($header, 'jk') || str_contains($header, 'sex') || str_contains($header, 'gender') || str_contains($header, 'l/p')) {
                    $colIndex['jenis_kelamin'] = $index;
                } elseif (str_contains($header, 'tanggal') || str_contains($header, 'tgl') || str_contains($header, 'masuk')) {
                    $colIndex['tanggal_masuk'] = $index;
                }
            }
            
            // Fallback to default indexes if headers are not found
            if ($colIndex['no_induk'] === -1) $colIndex['no_induk'] = 0;
            if ($colIndex['nama'] === -1) $colIndex['nama'] = 1;
            if ($colIndex['kelas'] === -1) $colIndex['kelas'] = 2;
            if ($colIndex['asrama'] === -1) $colIndex['asrama'] = 3;
            if ($colIndex['jenis_kelamin'] === -1) $colIndex['jenis_kelamin'] = 4;
            if ($colIndex['tanggal_masuk'] === -1) $colIndex['tanggal_masuk'] = 5;
            
            $importedCount = 0;
            $errors = [];
            
            DB::beginTransaction();
            
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                if (empty(array_filter($row))) {
                    continue;
                }
                
                $noInduk = trim((string)($row[$colIndex['no_induk']] ?? ''));
                $nama = trim((string)($row[$colIndex['nama']] ?? ''));
                $kelas = trim((string)($row[$colIndex['kelas']] ?? ''));
                $asrama = trim((string)($row[$colIndex['asrama']] ?? ''));
                $jkRaw = strtoupper(trim((string)($row[$colIndex['jenis_kelamin']] ?? '')));
                $tglMasukRaw = trim((string)($row[$colIndex['tanggal_masuk']] ?? ''));
                
                if (!$noInduk || !$nama || !$kelas) {
                    $errors[] = "Baris " . ($i + 1) . ": No Induk, Nama, dan Kelas wajib diisi.";
                    continue;
                }
                
                $jk = 'L';
                if ($jkRaw === 'P' || str_contains(strtolower($jkRaw), 'perempuan') || str_contains(strtolower($jkRaw), 'putri') || str_contains(strtolower($jkRaw), 'p')) {
                    $jk = 'P';
                }
                
                $tanggalMasuk = null;
                if ($tglMasukRaw) {
                    if (is_numeric($tglMasukRaw)) {
                        $tanggalMasuk = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tglMasukRaw)->format('Y-m-d');
                    } else {
                        $time = strtotime($tglMasukRaw);
                        if ($time) {
                            $tanggalMasuk = date('Y-m-d', $time);
                        }
                    }
                }
                
                Siswa::updateOrCreate(
                    ['no_induk' => $noInduk],
                    [
                        'nama' => $nama,
                        'kelas' => $kelas,
                        'asrama' => $asrama ?: null,
                        'jenis_kelamin' => $jk,
                        'tanggal_masuk' => $tanggalMasuk,
                        'status' => 'aktif',
                    ]
                );
                
                $importedCount++;
            }
            
            if (!empty($errors)) {
                DB::rollBack();
                return back()->withErrors($errors);
            }
            
            DB::commit();
            
            return redirect()->route('master.siswa.index')
                ->with('sukses', "Berhasil mengimpor {$importedCount} data siswa.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal membaca atau memproses file Excel: ' . $e->getMessage()]);
        }
    }
}
