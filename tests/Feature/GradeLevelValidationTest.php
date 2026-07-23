<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\MasterTarifSpp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class GradeLevelValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private TahunAjaran $tahunAjaran;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->tahunAjaran = TahunAjaran::create([
            'nama' => '2026/2027',
            'is_aktif' => true,
        ]);
    }

    public function test_store_siswa_validation_only_accepts_valid_grades(): void
    {
        $this->actingAs($this->user);

        $validGrades = ['7', '8', '9', '7A', '8-B', '9 C'];
        $invalidGrades = ['6', 'X', 'XII', 'Kelas 6', '1', 'Kelas 10', 'sd 3', 'smp', 'kelas 7', 'VII', 'VIII', 'IX', 'VIII-B', 'IX C'];

        foreach ($validGrades as $grade) {
            $response = $this->post(route('master.siswa.store'), [
                'no_induk' => 'NIS-' . uniqid(),
                'nama' => 'Siswa ' . $grade,
                'kelas' => $grade,
                'jenis_kelamin' => 'L',
                'status' => 'aktif',
            ]);
            $response->assertSessionHasNoErrors();
            $response->assertRedirect(route('master.siswa.index'));
        }

        foreach ($invalidGrades as $grade) {
            $response = $this->post(route('master.siswa.store'), [
                'no_induk' => 'NIS-' . uniqid(),
                'nama' => 'Siswa ' . $grade,
                'kelas' => $grade,
                'jenis_kelamin' => 'L',
                'status' => 'aktif',
            ]);
            $response->assertSessionHasErrors('kelas');
        }
    }

    public function test_update_siswa_validation_only_accepts_valid_grades(): void
    {
        $this->actingAs($this->user);

        $siswa = Siswa::create([
            'no_induk' => 'NIS-001',
            'nama' => 'Siswa Asal',
            'kelas' => '7A',
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
        ]);

        $validGrades = ['7', '8', '9', '7A', '8-B', '9 C'];
        $invalidGrades = ['6', 'X', 'XII', 'Kelas 6', '1', 'Kelas 10', 'sd 3', 'smp', 'kelas 7', 'VII', 'VIII', 'IX', 'VIII-B', 'IX C'];

        foreach ($validGrades as $grade) {
            $response = $this->put(route('master.siswa.update', $siswa), [
                'no_induk' => 'NIS-001',
                'nama' => 'Siswa Asal',
                'kelas' => $grade,
                'jenis_kelamin' => 'L',
                'status' => 'aktif',
            ]);
            $response->assertSessionHasNoErrors();
            $response->assertRedirect(route('master.siswa.index'));
        }

        foreach ($invalidGrades as $grade) {
            $response = $this->put(route('master.siswa.update', $siswa), [
                'no_induk' => 'NIS-001',
                'nama' => 'Siswa Asal',
                'kelas' => $grade,
                'jenis_kelamin' => 'L',
                'status' => 'aktif',
            ]);
            $response->assertSessionHasErrors('kelas');
        }
    }

    public function test_store_tarif_spp_validation_only_accepts_valid_grades(): void
    {
        $this->actingAs($this->user);

        $validGrades = ['7', '8', '9', '7A', '8-B', '9 C'];
        $invalidGrades = ['6', 'X', 'XII', 'Kelas 6', '1', 'Kelas 10', 'sd 3', 'smp', 'kelas 7', 'VII', 'VIII', 'IX', 'VIII-B', 'IX C'];

        foreach ($validGrades as $grade) {
            $response = $this->post(route('master.tarif-spp.store'), [
                'tahun_ajaran_id' => $this->tahunAjaran->id,
                'kelas' => $grade,
                'tarif' => 150000,
            ]);
            $response->assertSessionHasNoErrors();
            $response->assertRedirect(route('master.tarif-spp.index'));
        }

        foreach ($invalidGrades as $grade) {
            $response = $this->post(route('master.tarif-spp.store'), [
                'tahun_ajaran_id' => $this->tahunAjaran->id,
                'kelas' => $grade,
                'tarif' => 150000,
            ]);
            $response->assertSessionHasErrors('kelas');
        }
    }

    public function test_update_tarif_spp_validation_only_accepts_valid_grades(): void
    {
        $this->actingAs($this->user);

        $tarifSpp = MasterTarifSpp::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'kelas' => '7A',
            'tarif' => 150000,
        ]);

        $validGrades = ['7', '8', '9', '7A', '8-B', '9 C'];
        $invalidGrades = ['6', 'X', 'XII', 'Kelas 6', '1', 'Kelas 10', 'sd 3', 'smp', 'kelas 7', 'VII', 'VIII', 'IX', 'VIII-B', 'IX C'];

        foreach ($validGrades as $grade) {
            $response = $this->put(route('master.tarif-spp.update', $tarifSpp), [
                'kelas' => $grade,
                'tarif' => 150000,
            ]);
            $response->assertSessionHasNoErrors();
            $response->assertRedirect(route('master.tarif-spp.index'));
        }

        foreach ($invalidGrades as $grade) {
            $response = $this->put(route('master.tarif-spp.update', $tarifSpp), [
                'kelas' => $grade,
                'tarif' => 150000,
            ]);
            $response->assertSessionHasErrors('kelas');
        }
    }

    public function test_excel_import_rejects_invalid_grades(): void
    {
        $this->actingAs($this->user);

        // Buat file Excel test dengan satu baris valid dan satu baris tidak valid kelasnya
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'No. Induk');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Kelas');
        $sheet->setCellValue('D1', 'Asrama');
        $sheet->setCellValue('E1', 'Jenis Kelamin');
        $sheet->setCellValue('F1', 'Tanggal Masuk');

        // Row 2: Valid
        $sheet->setCellValue('A2', '99101');
        $sheet->setCellValue('B2', 'Siswa Valid');
        $sheet->setCellValue('C2', '7A');
        $sheet->setCellValue('D2', 'Asrama A');
        $sheet->setCellValue('E2', 'L');
        $sheet->setCellValue('F2', '2026-06-01');

        // Row 3: Invalid grade (e.g. Kelas 6)
        $sheet->setCellValue('A3', '99102');
        $sheet->setCellValue('B3', 'Siswa Invalid');
        $sheet->setCellValue('C3', '6A');
        $sheet->setCellValue('D3', 'Asrama B');
        $sheet->setCellValue('E3', 'P');
        $sheet->setCellValue('F3', '2026-06-01');

        $tempFile = tempnam(sys_get_temp_dir(), 'excel_import_test_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        $uploadedFile = new UploadedFile(
            $tempFile,
            'siswa_import_test.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->post(route('master.siswa.import'), [
            'file' => $uploadedFile,
        ]);

        // Harus gagal validasi dan tidak ada baris yang disimpan (karena transaksional)
        $response->assertSessionHasErrors('import');
        
        $this->assertDatabaseMissing('siswa', [
            'no_induk' => '99101',
        ]);
        $this->assertDatabaseMissing('siswa', [
            'no_induk' => '99102',
        ]);

        if (file_exists($tempFile)) {
            @unlink($tempFile);
        }
    }
}
