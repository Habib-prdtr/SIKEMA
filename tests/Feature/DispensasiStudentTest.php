<?php

namespace Tests\Feature;

use App\Models\Dispensasi;
use App\Models\Siswa;
use App\Models\SiswaTahunAjaran;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\TagihanSpp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DispensasiStudentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private TahunAjaran $tahunAjaran;
    private Dispensasi $dispensasi;
    private SiswaTahunAjaran $siswaTahunAjaran;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->tahunAjaran = TahunAjaran::create([
            'nama' => '2026/2027',
            'is_aktif' => true,
        ]);

        $this->dispensasi = Dispensasi::create([
            'nama' => 'Beasiswa Yatim',
            'tipe_potongan' => 'persen',
            'nilai_potongan' => 50,
        ]);

        $siswa = Siswa::create([
            'no_induk' => 'NIS-001',
            'nama' => 'Ahmad Fulan',
            'kelas' => '7A',
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
        ]);

        $this->siswaTahunAjaran = SiswaTahunAjaran::create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'tarif_spp' => 100000,
        ]);

        // Generate 12 bills for student
        for ($month = 1; $month <= 12; $month++) {
            TagihanSpp::create([
                'siswa_tahun_ajaran_id' => $this->siswaTahunAjaran->id,
                'bulan' => $month,
                'tahun' => 2026,
                'tagihan' => 100000,
                'status' => 'belum',
            ]);
        }
    }

    public function test_can_access_dispensasi_siswa_page(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('master.dispensasi.siswa', $this->dispensasi));
        $response->assertStatus(200);
        $response->assertSee($this->dispensasi->nama);
        $response->assertSee('Ahmad Fulan');
    }

    public function test_can_assign_dispensasi_to_student_and_recalculates_bills(): void
    {
        $this->actingAs($this->user);

        // Assign dispensation for 6 months
        $response = $this->post(route('master.dispensasi.siswa.store', $this->dispensasi), [
            'siswa_tahun_ajaran_id' => $this->siswaTahunAjaran->id,
            'durasi_dispensasi' => 6,
        ]);

        $response->assertRedirect(route('master.dispensasi.siswa', $this->dispensasi));
        $response->assertSessionHas('sukses');

        $this->siswaTahunAjaran->refresh();
        $this->assertEquals($this->dispensasi->id, $this->siswaTahunAjaran->dispensasi_id);
        $this->assertEquals(6, $this->siswaTahunAjaran->durasi_dispensasi);

        // Check bills: first 6 bills should be discounted by 50% (100k -> 50k), next 6 should remain 100k
        $bills = $this->siswaTahunAjaran->tagihanSpp()->orderBy('id')->get();
        for ($i = 0; $i < 6; $i++) {
            $this->assertEquals(50000, $bills[$i]->tagihan);
        }
        for ($i = 6; $i < 12; $i++) {
            $this->assertEquals(100000, $bills[$i]->tagihan);
        }
    }

    public function test_can_remove_dispensasi_from_student_and_resets_bills(): void
    {
        $this->actingAs($this->user);

        // Set dispensation first
        $this->siswaTahunAjaran->update([
            'dispensasi_id' => $this->dispensasi->id,
            'durasi_dispensasi' => 6,
        ]);
        
        // Run initial discount on bills
        foreach ($this->siswaTahunAjaran->tagihanSpp as $index => $bill) {
            if ($index < 6) {
                $bill->update(['tagihan' => 50000]);
            }
        }

        // Remove dispensation
        $response = $this->delete(route('master.dispensasi.siswa.destroy', [$this->dispensasi, $this->siswaTahunAjaran]));

        $response->assertRedirect(route('master.dispensasi.siswa', $this->dispensasi));
        $response->assertSessionHas('sukses');

        $this->siswaTahunAjaran->refresh();
        $this->assertNull($this->siswaTahunAjaran->dispensasi_id);
        $this->assertEquals(0, $this->siswaTahunAjaran->durasi_dispensasi);

        // Check bills: all 12 bills should reset back to 100k
        $bills = $this->siswaTahunAjaran->tagihanSpp()->get();
        foreach ($bills as $bill) {
            $this->assertEquals(100000, $bill->tagihan);
        }
    }
}
