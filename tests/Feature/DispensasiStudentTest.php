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

    public function test_can_bulk_assign_dispensasi_to_multiple_students(): void
    {
        $this->actingAs($this->user);

        $siswa2 = Siswa::create([
            'no_induk' => 'NIS-002',
            'nama' => 'Budi Santoso',
            'kelas' => '7A',
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
        ]);

        $sta2 = SiswaTahunAjaran::create([
            'siswa_id' => $siswa2->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'tarif_spp' => 100000,
        ]);

        // Bulk assign dispensation to both students for 6 months
        $response = $this->post(route('master.dispensasi.siswa.store', $this->dispensasi), [
            'siswa_tahun_ajaran_ids' => [$this->siswaTahunAjaran->id, $sta2->id],
            'durasi_dispensasi' => 6,
        ]);

        $response->assertRedirect(route('master.dispensasi.siswa', $this->dispensasi));
        $response->assertSessionHas('sukses');

        $this->siswaTahunAjaran->refresh();
        $sta2->refresh();

        $this->assertEquals($this->dispensasi->id, $this->siswaTahunAjaran->dispensasi_id);
        $this->assertEquals(6, $this->siswaTahunAjaran->durasi_dispensasi);
        $this->assertEquals($this->dispensasi->id, $sta2->dispensasi_id);
        $this->assertEquals(6, $sta2->durasi_dispensasi);
    }

    public function test_can_assign_dispensasi_for_semester_genap(): void
    {
        $this->actingAs($this->user);

        // Assign dispensation specifically for Semester Genap (6 months)
        $response = $this->post(route('master.dispensasi.siswa.store', $this->dispensasi), [
            'siswa_tahun_ajaran_id' => $this->siswaTahunAjaran->id,
            'durasi_dispensasi' => 6,
            'semester_dispensasi' => 'genap',
        ]);

        $response->assertRedirect(route('master.dispensasi.siswa', $this->dispensasi));
        $response->assertSessionHas('sukses');

        $this->siswaTahunAjaran->refresh();

        // Check bills: Semester Ganjil (months 7-12) remain 100k, Semester Genap (months 1-6) discounted by 50% (50k)
        $billsGanjil = $this->siswaTahunAjaran->tagihanSpp()->whereIn('bulan', [7, 8, 9, 10, 11, 12])->get();
        foreach ($billsGanjil as $bill) {
            $this->assertEquals(100000, $bill->tagihan);
        }

        $billsGenap = $this->siswaTahunAjaran->tagihanSpp()->whereIn('bulan', [1, 2, 3, 4, 5, 6])->get();
        foreach ($billsGenap as $bill) {
            $this->assertEquals(50000, $bill->tagihan);
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

    public function test_updating_dispensasi_recalculates_attached_student_bills(): void
    {
        $this->actingAs($this->user);

        // Assign initial dispensation (50% discount)
        $this->post(route('master.dispensasi.siswa.store', $this->dispensasi), [
            'siswa_tahun_ajaran_id' => $this->siswaTahunAjaran->id,
            'durasi_dispensasi' => 6,
        ]);

        // Verify initial discount (50k for first 6 bills)
        $bills = $this->siswaTahunAjaran->tagihanSpp()->orderBy('id')->get();
        $this->assertEquals(50000, $bills[0]->tagihan);

        // Edit dispensasi: change to nominal discount of 70,000 (meaning bill becomes 30,000)
        $response = $this->put(route('master.dispensasi.update', $this->dispensasi), [
            'nama' => 'Beasiswa Yatim Updated',
            'tipe_potongan' => 'nominal',
            'nilai_potongan' => 70000,
        ]);

        $response->assertRedirect(route('master.dispensasi.index'));
        $response->assertSessionHas('sukses');

        // Check bills: first 6 bills should now be 30,000 (100k - 70k)
        $updatedBills = $this->siswaTahunAjaran->tagihanSpp()->orderBy('id')->get();
        for ($i = 0; $i < 6; $i++) {
            $this->assertEquals(30000, $updatedBills[$i]->tagihan);
        }
        for ($i = 6; $i < 12; $i++) {
            $this->assertEquals(100000, $updatedBills[$i]->tagihan);
        }
    }

    public function test_can_process_payment_with_100_percent_dispensasi_and_nominal_0(): void
    {
        $this->actingAs($this->user);

        // Create 100% discount dispensasi
        $fullDispensasi = Dispensasi::create([
            'nama' => 'Beasiswa Yatim Full 100%',
            'tipe_potongan' => 'persen',
            'nilai_potongan' => 100,
        ]);

        // Assign 100% dispensasi to student for 6 months
        $this->post(route('master.dispensasi.siswa.store', $fullDispensasi), [
            'siswa_tahun_ajaran_id' => $this->siswaTahunAjaran->id,
            'durasi_dispensasi' => 6,
        ]);

        $bill = $this->siswaTahunAjaran->tagihanSpp()->orderBy('id')->first();
        $this->assertEquals(0, $bill->tagihan);
        $this->assertEquals('belum', $bill->status);

        // Process payment for this Rp 0 bill
        $response = $this->post(route('penerimaan.store'), [
            'siswa_tahun_ajaran_id' => $this->siswaTahunAjaran->id,
            'tanggal' => now()->format('Y-m-d'),
            'items' => [
                'spp' => [$bill->id],
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Check bill: should be marked as lunas
        $bill->refresh();
        $this->assertEquals('lunas', $bill->status);
        $this->assertEquals(0, $bill->terbayar);

        // Check database transaction created
        $this->assertDatabaseHas('transaksi', [
            'siswa_tahun_ajaran_id' => $this->siswaTahunAjaran->id,
            'total_bayar' => 0,
        ]);
    }
}
