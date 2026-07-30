<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class SiswaImportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test import siswa dari file Excel.
     */
    public function test_import_siswa_via_excel(): void
    {
        // 1. Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. Buat file Excel test
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Siswa');
        
        // Write headers
        $sheet->setCellValue('A1', 'No. Induk');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Kelas');
        $sheet->setCellValue('D1', 'Asrama');
        $sheet->setCellValue('E1', 'Jenis Kelamin');
        $sheet->setCellValue('F1', 'Tanggal Masuk');

        // Row 2: Format tanggal string
        $sheet->setCellValue('A2', '99001');
        $sheet->setCellValue('B2', 'Siswa Test Satu');
        $sheet->setCellValue('C2', '7A');
        $sheet->setCellValue('D2', 'Putra A');
        $sheet->setCellValue('E2', 'L');
        $sheet->setCellValue('F2', '2026-06-01');

        // Row 3: Format tanggal angka Excel & gender P
        $sheet->setCellValue('A3', '99002');
        $sheet->setCellValue('B3', 'Siswa Test Dua');
        $sheet->setCellValue('C3', '8B');
        $sheet->setCellValue('D3', 'Putri B');
        $sheet->setCellValue('E3', 'P');
        $sheet->setCellValue('F3', '46174'); // Nilai tanggal di Excel

        // Simpan ke temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_import_test_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        // Ubah ke instance UploadedFile Laravel
        $uploadedFile = new UploadedFile(
            $tempFile,
            'siswa_import_test.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true // testMode
        );

        // 3. Post data ke router import
        $response = $this->post(route('master.siswa.import'), [
            'file' => $uploadedFile,
        ]);

        // 4. Pastikan redirect sukses dan data tersimpan di DB
        $response->assertRedirect(route('master.siswa.index'));
        $response->assertSessionHas('sukses');

        $this->assertDatabaseHas('siswa', [
            'no_induk' => '99001',
            'nama' => 'Siswa Test Satu',
            'kelas' => '7A',
            'jenis_kelamin' => 'L',
            'tanggal_masuk' => '2026-06-01 00:00:00',
        ]);

        $this->assertDatabaseHas('siswa', [
            'no_induk' => '99002',
            'nama' => 'Siswa Test Dua',
            'kelas' => '8B',
            'jenis_kelamin' => 'P',
        ]);

        // Hapus file temporary
        if (file_exists($tempFile)) {
            @unlink($tempFile);
        }
    }
}
