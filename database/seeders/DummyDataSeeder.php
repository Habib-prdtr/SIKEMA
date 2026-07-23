<?php

namespace Database\Seeders;

use App\Models\JenisPenerimaan;
use App\Models\MasterTarifSpp;
use App\Models\Pengeluaran;
use App\Models\PosBiaya;
use App\Models\SaldoAwal;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\SiswaTahunAjaran;
use App\Models\TagihanIuran;
use App\Models\TagihanSpp;
use App\Models\TahunAjaran;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\User;
use App\Services\TagihanService;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DummyDataSeeder extends Seeder
{
    /**
     * Seed seluruh dummy data secara programmatik.
     *
     * Data yang di-seed:
     * - 1 data sekolah
     * - 3 tahun ajaran (2023/2024, 2024/2025, 2025/2026)
     * - 3 users (admin, bendahara, staff_tu)
     * - 9 master tarif SPP (3 kelas per tahun ajaran)
     * - 9 jenis penerimaan / iuran (3 per tahun ajaran)
     * - 30 pos_biaya (10 per tahun ajaran)
     * - 3 saldo_awal (1 per tahun ajaran)
     * - ~300 siswa (100 per angkatan: 2023, 2024, 2025)
     * - ~600 siswa_tahun_ajaran (siswa aktif di tahun ajaran masing-masing)
     * - ~7.200 tagihan SPP (600 STA x 12 bulan)
     * - ~1.800 tagihan iuran (600 STA x 3 jenis iuran)
     * - Riwayat transaksi pembayaran bulanan (lunas, terlambat, atau menunggak)
     * - Pengeluaran operasional bulanan per pos biaya
     *
     * Usage:
     *   php artisan db:seed --class=DummyDataSeeder
     */

    private \Faker\Generator $faker;
    private TagihanService $tagihanService;
    private int $trxCounter = 0;

    // Profil pembayaran siswa (distribusi acak)
    private const PROFIL_BAYAR_RAJIN    = 'rajin';    // ~50% - bayar setiap bulan tepat waktu
    private const PROFIL_BAYAR_TERLAMBAT = 'terlambat'; // ~30% - bayar tapi sering terlambat
    private const PROFIL_BAYAR_NUNGGAK  = 'nunggak';  // ~20% - ada bulan yang tidak dibayar

    public function __construct(TagihanService $tagihanService)
    {
        $this->tagihanService = $tagihanService;
    }

    public function run(): void
    {
        $this->faker = Faker::create('id_ID');

        $this->command->info('Membersihkan database sebelum seeding...');
        $this->truncateTables();

        $this->command->info('Membuat data sekolah...');
        $sekolah = $this->seedSekolah();

        $this->command->info('Membuat users...');
        $users = $this->seedUsers();

        $this->command->info('Membuat tahun ajaran...');
        $tahunAjaranList = $this->seedTahunAjaran();

        $this->command->info('Membuat master tarif SPP...');
        $this->seedMasterTarifSpp($tahunAjaranList);

        $this->command->info('Membuat jenis penerimaan (iuran)...');
        $this->seedJenisPenerimaan($tahunAjaranList);

        $this->command->info('Membuat pos biaya...');
        $this->seedPosBiaya($tahunAjaranList);

        $this->command->info('Membuat saldo awal...');
        $this->seedSaldoAwal($tahunAjaranList);

        $this->command->info('Membuat data siswa...');
        $siswaList = $this->seedSiswa();

        $this->command->info('Mengaktifkan siswa ke tahun ajaran dan membuat tagihan...');
        $this->seedSiswaTahunAjaran($siswaList, $tahunAjaranList);

        $this->command->info('Mensimulasikan riwayat pembayaran...');
        $this->seedTransaksi($tahunAjaranList, $users);

        $this->command->info('Membuat data pengeluaran operasional...');
        $this->seedPengeluaran($tahunAjaranList, $users);

        $this->resetSequences();
        $this->printSummary();
    }

    // =========================================================
    // Truncate
    // =========================================================

    private function truncateTables(): void
    {
        $tables = [
            'transaksi_detail', 'transaksi', 'tagihan_spp', 'tagihan_iuran',
            'siswa_tahun_ajaran', 'master_tarif_spp', 'jenis_penerimaan',
            'pengeluaran', 'pos_biaya', 'saldo_awal', 'siswa', 'tahun_ajaran',
            'users', 'sekolah',
        ];

        Schema::disableForeignKeyConstraints();
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
        Schema::enableForeignKeyConstraints();
    }

    // =========================================================
    // Seed: Sekolah
    // =========================================================

    private function seedSekolah(): Sekolah
    {
        return Sekolah::create([
            'nama_sekolah'  => 'Madrasah Tsanawiyah Al-Hikmah',
            'nama_yayasan'  => 'Yayasan Pendidikan Islam Al-Hikmah',
            'alamat'        => 'Jl. K.H. Ahmad Dahlan No. 12, Kebumen, Jawa Tengah 54312',
            'telepon'       => '(0287) 381234',
            'email'         => 'mts.alhikmah@gmail.com',
            'kepala_tu'     => 'Drs. H. Ahmad Fauzi, M.Pd.',
            'nip_kepala_tu' => '196804151994031007',
        ]);
    }

    // =========================================================
    // Seed: Users
    // =========================================================

    private function seedUsers(): array
    {
        $password = Hash::make('admin123');

        $admin = User::create([
            'name'     => 'Administrator',
            'username' => 'admin',
            'email'    => 'admin@mts-alhikmah.sch.id',
            'password' => $password,
        ]);

        $bendahara = User::create([
            'name'     => 'Siti Maryam, S.E.',
            'username' => 'bendahara',
            'email'    => 'bendahara@mts-alhikmah.sch.id',
            'password' => $password,
        ]);

        $staffTu = User::create([
            'name'     => 'Budi Santoso, A.Md.',
            'username' => 'staff_tu',
            'email'    => 'stafftu@mts-alhikmah.sch.id',
            'password' => $password,
        ]);

        $this->command->info('  Password semua user: admin123');

        return ['admin' => $admin, 'bendahara' => $bendahara, 'staff_tu' => $staffTu];
    }

    // =========================================================
    // Seed: Tahun Ajaran
    // =========================================================

    private function seedTahunAjaran(): array
    {
        $data = [
            ['nama' => '2023/2024', 'is_aktif' => false],
            ['nama' => '2024/2025', 'is_aktif' => false],
            ['nama' => '2025/2026', 'is_aktif' => true],
        ];

        $result = [];
        foreach ($data as $item) {
            $result[] = TahunAjaran::create($item);
        }

        return $result;
    }

    // =========================================================
    // Seed: Master Tarif SPP
    // =========================================================

    private function seedMasterTarifSpp(array $tahunAjaranList): void
    {
        // Tarif SPP naik setiap tahun ajaran
        $tarifPerTahun = [
            '2023/2024' => ['VII' => 200000, 'VIII' => 200000, 'IX' => 200000],
            '2024/2025' => ['VII' => 225000, 'VIII' => 225000, 'IX' => 225000],
            '2025/2026' => ['VII' => 250000, 'VIII' => 250000, 'IX' => 250000],
        ];

        foreach ($tahunAjaranList as $ta) {
            $tarif = $tarifPerTahun[$ta->nama] ?? ['VII' => 200000, 'VIII' => 200000, 'IX' => 200000];
            foreach ($tarif as $kelas => $nominal) {
                MasterTarifSpp::create([
                    'tahun_ajaran_id' => $ta->id,
                    'kelas'           => $kelas,
                    'tarif'           => $nominal,
                ]);
            }
        }
    }

    // =========================================================
    // Seed: Jenis Penerimaan (Iuran)
    // =========================================================

    private function seedJenisPenerimaan(array $tahunAjaranList): void
    {
        // Iuran yang berbeda tiap tahun ajaran, dengan kenaikan tarif
        $iuranPerTahun = [
            '2023/2024' => [
                ['urutan' => 1, 'nama' => 'Iuran OSIS',        'tarif' => 100000],
                ['urutan' => 2, 'nama' => 'Iuran Pramuka',     'tarif' =>  75000],
                ['urutan' => 3, 'nama' => 'Iuran Perpustakaan','tarif' =>  50000],
            ],
            '2024/2025' => [
                ['urutan' => 1, 'nama' => 'Iuran OSIS',        'tarif' => 110000],
                ['urutan' => 2, 'nama' => 'Iuran Pramuka',     'tarif' =>  80000],
                ['urutan' => 3, 'nama' => 'Iuran Perpustakaan','tarif' =>  60000],
            ],
            '2025/2026' => [
                ['urutan' => 1, 'nama' => 'Iuran OSIS',        'tarif' => 120000],
                ['urutan' => 2, 'nama' => 'Iuran Pramuka',     'tarif' =>  90000],
                ['urutan' => 3, 'nama' => 'Iuran Perpustakaan','tarif' =>  75000],
            ],
        ];

        foreach ($tahunAjaranList as $ta) {
            $iuranList = $iuranPerTahun[$ta->nama] ?? [];
            foreach ($iuranList as $iuran) {
                JenisPenerimaan::create(array_merge($iuran, [
                    'tahun_ajaran_id' => $ta->id,
                    'is_aktif'        => true,
                ]));
            }
        }
    }

    // =========================================================
    // Seed: Pos Biaya
    // =========================================================

    private function seedPosBiaya(array $tahunAjaranList): void
    {
        $posBiayaTemplate = [
            ['nama' => 'Gaji Tenaga Kependidikan',  'anggaran' => 60000000,  'keterangan' => 'Biaya gaji tenaga administrasi dan kebersihan'],
            ['nama' => 'Pemeliharaan Gedung',        'anggaran' => 15000000,  'keterangan' => 'Renovasi dan perawatan rutin bangunan sekolah'],
            ['nama' => 'Operasional Kantor',         'anggaran' => 8000000,   'keterangan' => 'ATK, fotokopi, dan keperluan administrasi'],
            ['nama' => 'Listrik dan Air',            'anggaran' => 10000000,  'keterangan' => 'Tagihan listrik dan air bulanan'],
            ['nama' => 'Internet dan Komunikasi',    'anggaran' => 5000000,   'keterangan' => 'Biaya internet dan pulsa operasional'],
            ['nama' => 'Kegiatan Siswa',             'anggaran' => 12000000,  'keterangan' => 'Ekstrakurikuler, lomba, dan kegiatan kesiswaan'],
            ['nama' => 'Pengembangan Perpustakaan',  'anggaran' => 7000000,   'keterangan' => 'Pengadaan buku dan bahan bacaan baru'],
            ['nama' => 'Transportasi Dinas',         'anggaran' => 4000000,   'keterangan' => 'Biaya perjalanan dinas guru dan staf'],
            ['nama' => 'Konsumsi Rapat',             'anggaran' => 3000000,   'keterangan' => 'Konsumsi rapat rutin dan pertemuan wali murid'],
            ['nama' => 'Dana Sosial dan Darurat',    'anggaran' => 5000000,   'keterangan' => 'Dana cadangan untuk keperluan mendadak'],
        ];

        foreach ($tahunAjaranList as $ta) {
            foreach ($posBiayaTemplate as $pos) {
                // Naik anggaran ~5% setiap tahun
                $multiplier = match ($ta->nama) {
                    '2023/2024' => 1.0,
                    '2024/2025' => 1.05,
                    '2025/2026' => 1.10,
                    default     => 1.0,
                };

                PosBiaya::create([
                    'tahun_ajaran_id' => $ta->id,
                    'nama'            => $pos['nama'],
                    'anggaran'        => (int) round($pos['anggaran'] * $multiplier),
                    'keterangan'      => $pos['keterangan'],
                    'is_aktif'        => true,
                ]);
            }
        }
    }

    // =========================================================
    // Seed: Saldo Awal
    // =========================================================

    private function seedSaldoAwal(array $tahunAjaranList): void
    {
        $saldoAwal = [
            '2023/2024' => 25000000,
            '2024/2025' => 31500000,
            '2025/2026' => 28750000,
        ];

        foreach ($tahunAjaranList as $ta) {
            SaldoAwal::create([
                'tahun_ajaran_id' => $ta->id,
                'jumlah'          => $saldoAwal[$ta->nama] ?? 0,
                'keterangan'      => 'Saldo awal tahun ajaran ' . $ta->nama . ' (saldo carry-over)',
            ]);
        }
    }

    // =========================================================
    // Seed: Siswa
    // =========================================================

    private function seedSiswa(): array
    {
        $this->faker->seed(12345); // Seed faker agar nama konsisten
        $siswaList = [];

        // Angkatan 2023 → masuk 2023, kelas VII → VIII → IX
        // Angkatan 2024 → masuk 2024, kelas VII → VIII
        // Angkatan 2025 → masuk 2025, kelas VII
        $angkatan = [2023, 2024, 2025];
        $counter = 1;

        foreach ($angkatan as $tahunMasuk) {
            for ($i = 1; $i <= 100; $i++) {
                $jenisKelamin = $this->faker->randomElement(['L', 'P']);
                $namaDepan = $jenisKelamin === 'L'
                    ? $this->faker->firstNameMale()
                    : $this->faker->firstNameFemale();

                $noInduk = str_pad($tahunMasuk, 4, '0', STR_PAD_LEFT)
                         . str_pad($i, 3, '0', STR_PAD_LEFT);

                $siswa = Siswa::create([
                    'no_induk'      => $noInduk,
                    'nama'          => $namaDepan . ' ' . $this->faker->lastName(),
                    'kelas'         => $this->getKelasAwal($tahunMasuk),
                    'asrama'        => $this->faker->randomElement(['Putra A', 'Putra B', 'Putri A', 'Putri B', null]),
                    'jenis_kelamin' => $jenisKelamin,
                    'tanggal_masuk' => Carbon::create($tahunMasuk, 7, mt_rand(1, 15)),
                    'status'        => $this->getStatusSiswa($tahunMasuk),
                ]);

                $siswaList[$tahunMasuk][] = $siswa;
                $counter++;
            }
        }

        return $siswaList;
    }

    private function getKelasAwal(int $tahunMasuk): string
    {
        // Kelas saat ini berdasarkan tahun masuk (tahun aktif = 2025/2026)
        return match ($tahunMasuk) {
            2023    => 'IX',
            2024    => 'VIII',
            2025    => 'VII',
            default => 'VII',
        };
    }

    private function getStatusSiswa(int $tahunMasuk): string
    {
        // Angkatan 2023 sudah selesai/lulus di 2025/2026, sisanya aktif
        if ($tahunMasuk === 2023) {
            return Siswa::STATUS_AKTIF; // Masih aktif di kelas IX
        }

        return Siswa::STATUS_AKTIF;
    }

    // =========================================================
    // Seed: Siswa Tahun Ajaran + Tagihan
    // =========================================================

    private function seedSiswaTahunAjaran(array $siswaList, array $tahunAjaranList): void
    {
        // Map tahun ajaran by nama untuk kemudahan akses
        $taMap = [];
        foreach ($tahunAjaranList as $ta) {
            $taMap[$ta->nama] = $ta;
        }

        // Map tarif SPP by tahun_ajaran_id dan kelas
        $tarifMap = [];
        foreach (MasterTarifSpp::all() as $tarif) {
            $tarifMap[$tarif->tahun_ajaran_id][$tarif->kelas] = $tarif->tarif;
        }

        /*
         * Aturan aktivasi siswa per tahun ajaran:
         * - Angkatan 2023: aktif di TA 2023/2024 (kelas VII), 2024/2025 (VIII), 2025/2026 (IX)
         * - Angkatan 2024: aktif di TA 2024/2025 (kelas VII), 2025/2026 (VIII)
         * - Angkatan 2025: aktif di TA 2025/2026 (kelas VII)
         */
        $aktivasiConfig = [
            2023 => [
                '2023/2024' => 'VII',
                '2024/2025' => 'VIII',
                '2025/2026' => 'IX',
            ],
            2024 => [
                '2024/2025' => 'VII',
                '2025/2026' => 'VIII',
            ],
            2025 => [
                '2025/2026' => 'VII',
            ],
        ];

        foreach ($siswaList as $angkatan => $siswaAngkatan) {
            $config = $aktivasiConfig[$angkatan] ?? [];

            foreach ($siswaAngkatan as $siswa) {
                foreach ($config as $namaTa => $kelas) {
                    $ta = $taMap[$namaTa] ?? null;
                    if (! $ta) {
                        continue;
                    }

                    $tarifSpp = $tarifMap[$ta->id][$kelas] ?? 200000;

                    $sta = SiswaTahunAjaran::create([
                        'siswa_id'         => $siswa->id,
                        'tahun_ajaran_id'  => $ta->id,
                        'tarif_spp'        => $tarifSpp,
                        'dispensasi_id'    => null,
                        'durasi_dispensasi'=> null,
                        'tunggakan_awal'   => 0,
                    ]);

                    // Generate tagihan SPP 12 bulan
                    $this->tagihanService->generateSpp($sta);

                    // Generate tagihan iuran
                    $this->tagihanService->generateIuranUntukSiswa($sta);
                }
            }
        }
    }

    // =========================================================
    // Seed: Transaksi Pembayaran
    // =========================================================

    private function seedTransaksi(array $tahunAjaranList, array $users): void
    {
        $operator = $users['bendahara'];

        foreach ($tahunAjaranList as $ta) {
            $tahunAwal = (int) substr($ta->nama, 0, 4);

            // Hanya simulasikan pembayaran untuk tahun ajaran yang sudah atau sedang berjalan
            // TA 2025/2026 = tahun ini, simulasikan sampai bulan Juni 2026 (belum selesai)
            $batasanBulan = $this->getBatasanBulan($ta->nama);

            $staList = SiswaTahunAjaran::with(['tagihanSpp', 'tagihanIuran'])
                ->where('tahun_ajaran_id', $ta->id)
                ->get();

            foreach ($staList as $sta) {
                $profil = $this->randomProfilBayar();
                $this->simulasiPembayaranSiswa($sta, $ta, $profil, $operator, $batasanBulan);
            }

            $this->command->line("  TA {$ta->nama}: {$staList->count()} siswa diproses.");
        }
    }

    private function getBatasanBulan(string $namaTa): array
    {
        // Menentukan batas akhir bulan simulasi pembayaran
        $today = Carbon::now();
        $tahunAwal = (int) substr($namaTa, 0, 4);

        // Kalender: Juli tahun_awal - Juni tahun_awal+1
        $akhirTA = Carbon::create($tahunAwal + 1, 6, 30);

        if ($today->lt($akhirTA)) {
            // TA belum selesai: simulasikan sampai bulan lalu
            $batasAkhir = $today->copy()->subMonth();
        } else {
            // TA sudah selesai: simulasikan penuh (tapi tidak semuanya lunas)
            $batasAkhir = $akhirTA;
        }

        return ['bulan' => $batasAkhir->month, 'tahun' => $batasAkhir->year];
    }

    private function randomProfilBayar(): string
    {
        $rand = mt_rand(1, 100);
        if ($rand <= 50) return self::PROFIL_BAYAR_RAJIN;
        if ($rand <= 80) return self::PROFIL_BAYAR_TERLAMBAT;
        return self::PROFIL_BAYAR_NUNGGAK;
    }

    private function simulasiPembayaranSiswa(
        SiswaTahunAjaran $sta,
        TahunAjaran $ta,
        string $profil,
        User $operator,
        array $batasanBulan
    ): void {
        $tahunAwal = (int) substr($ta->nama, 0, 4);

        // Buat kalender bulan-bulan di tahun ajaran ini
        $kalender = [];
        for ($bulan = 7; $bulan <= 12; $bulan++) {
            $kalender[] = ['bulan' => $bulan, 'tahun' => $tahunAwal];
        }
        for ($bulan = 1; $bulan <= 6; $bulan++) {
            $kalender[] = ['bulan' => $bulan, 'tahun' => $tahunAwal + 1];
        }

        // Filter hanya bulan yang sudah lewat batas simulasi
        $batasCarbon = Carbon::create($batasanBulan['tahun'], $batasanBulan['bulan'], 1)->endOfMonth();
        $kalender = array_filter($kalender, function ($bt) use ($batasCarbon) {
            $tgl = Carbon::create($bt['tahun'], $bt['bulan'], 1);
            return $tgl->lte($batasCarbon);
        });

        foreach ($kalender as $bt) {
            $bayarSppBulanIni = $this->apakahBayarBulanIni($profil, $bt);
            if (! $bayarSppBulanIni) {
                continue;
            }

            // Cari tagihan SPP untuk bulan ini
            $tagihanSpp = $sta->tagihanSpp->firstWhere(fn($t) =>
                $t->bulan === $bt['bulan'] && $t->tahun === $bt['tahun']
            );

            if (! $tagihanSpp || $tagihanSpp->status === TagihanSpp::STATUS_LUNAS) {
                continue;
            }

            // Tentukan tanggal pembayaran
            $tglBayar = $this->tanggalBayar($profil, $bt['bulan'], $bt['tahun']);

            // Kumpulkan items transaksi untuk bulan ini
            $items = [];
            $totalBayar = 0;

            // Item SPP
            $nominalSpp = $tagihanSpp->tagihan;
            $items[] = [
                'jenis'              => 'spp',
                'bulan'              => $bt['bulan'],
                'tahun'              => $bt['tahun'],
                'nominal'            => $nominalSpp,
                'tagihan_id'         => $tagihanSpp->id,
            ];
            $totalBayar += $nominalSpp;

            // Jika bulan Juli (awal tahun ajaran), bayar iuran sekaligus
            if ($bt['bulan'] === 7) {
                foreach ($sta->tagihanIuran as $tagihanIuran) {
                    if ($tagihanIuran->status === TagihanIuran::STATUS_LUNAS) {
                        continue;
                    }
                    $items[] = [
                        'jenis'              => 'iuran',
                        'jenis_penerimaan_id'=> $tagihanIuran->jenis_penerimaan_id,
                        'tagihan_iuran_id'   => $tagihanIuran->id,
                        'nominal'            => $tagihanIuran->tagihan,
                    ];
                    $totalBayar += $tagihanIuran->tagihan;
                }
            }

            // Buat transaksi
            $this->buatTransaksi($sta, $ta, $operator, $tglBayar, $items, $totalBayar);
        }
    }

    private function apakahBayarBulanIni(string $profil, array $bt): bool
    {
        return match ($profil) {
            self::PROFIL_BAYAR_RAJIN     => true,                         // Selalu bayar
            self::PROFIL_BAYAR_TERLAMBAT => mt_rand(1, 10) > 1,          // 90% bayar
            self::PROFIL_BAYAR_NUNGGAK   => mt_rand(1, 10) > 3,          // 70% bayar
            default                       => true,
        };
    }

    private function tanggalBayar(string $profil, int $bulan, int $tahun): Carbon
    {
        return match ($profil) {
            self::PROFIL_BAYAR_RAJIN     => Carbon::create($tahun, $bulan, mt_rand(1, 10)),
            self::PROFIL_BAYAR_TERLAMBAT => Carbon::create($tahun, $bulan, mt_rand(11, 25)),
            self::PROFIL_BAYAR_NUNGGAK   => Carbon::create($tahun, $bulan, mt_rand(20, 28)),
            default                       => Carbon::create($tahun, $bulan, 15),
        };
    }

    private function buatTransaksi(
        SiswaTahunAjaran $sta,
        TahunAjaran $ta,
        User $operator,
        Carbon $tanggal,
        array $items,
        int $totalBayar
    ): void {
        $this->trxCounter++;
        $noTrx = 'TRX-' . str_pad($this->trxCounter, 5, '0', STR_PAD_LEFT);

        $transaksi = Transaksi::create([
            'no_transaksi'         => $noTrx,
            'siswa_tahun_ajaran_id'=> $sta->id,
            'tahun_ajaran_id'      => $ta->id,
            'user_id'              => $operator->id,
            'tanggal'              => $tanggal->toDateString(),
            'total_bayar'          => $totalBayar,
            'keterangan'           => null,
        ]);

        foreach ($items as $item) {
            TransaksiDetail::create([
                'transaksi_id'       => $transaksi->id,
                'jenis'              => $item['jenis'],
                'jenis_penerimaan_id'=> $item['jenis_penerimaan_id'] ?? null,
                'bulan'              => $item['bulan'] ?? null,
                'tahun'              => $item['tahun'] ?? null,
                'nominal'            => $item['nominal'],
            ]);

            // Update tagihan yang dibayar
            if ($item['jenis'] === 'spp') {
                $tagihanSpp = TagihanSpp::find($item['tagihan_id']);
                $tagihanSpp?->bayar($item['nominal']);
            } elseif ($item['jenis'] === 'iuran') {
                $tagihanIuran = TagihanIuran::find($item['tagihan_iuran_id']);
                $tagihanIuran?->bayar($item['nominal']);
            }
        }
    }

    // =========================================================
    // Seed: Pengeluaran Operasional
    // =========================================================

    private function seedPengeluaran(array $tahunAjaranList, array $users): void
    {
        $operator = $users['bendahara'];

        foreach ($tahunAjaranList as $ta) {
            $tahunAwal  = (int) substr($ta->nama, 0, 4);
            $posBiaya   = PosBiaya::where('tahun_ajaran_id', $ta->id)->get();
            $batasanBulan = $this->getBatasanBulan($ta->nama);
            $batasCarbon  = Carbon::create($batasanBulan['tahun'], $batasanBulan['bulan'], 1)->endOfMonth();

            // Kalender bulan TA
            $kalender = [];
            for ($b = 7; $b <= 12; $b++) {
                $kalender[] = ['bulan' => $b, 'tahun' => $tahunAwal];
            }
            for ($b = 1; $b <= 6; $b++) {
                $kalender[] = ['bulan' => $b, 'tahun' => $tahunAwal + 1];
            }

            foreach ($posBiaya as $pos) {
                foreach ($kalender as $bt) {
                    $tgl = Carbon::create($bt['tahun'], $bt['bulan'], 1);
                    if ($tgl->gt($batasCarbon)) {
                        continue;
                    }

                    // Tidak semua pos ada pengeluaran setiap bulan
                    if (mt_rand(1, 10) > 7) {
                        continue; // 30% bulan dilewati
                    }

                    // Jumlah pengeluaran: antara 30%-90% anggaran per bulan, dibagi 12
                    $anggaranBulanan = (int) round($pos->anggaran / 12);
                    $jumlah = (int) round($anggaranBulanan * (mt_rand(30, 90) / 100));

                    // Bulatkan ke kelipatan 1000
                    $jumlah = (int) round($jumlah / 1000) * 1000;

                    if ($jumlah < 10000) {
                        continue;
                    }

                    $tanggalBayar = Carbon::create($bt['tahun'], $bt['bulan'], mt_rand(5, 25));

                    Pengeluaran::create([
                        'pos_biaya_id' => $pos->id,
                        'user_id'      => $operator->id,
                        'tanggal'      => $tanggalBayar->toDateString(),
                        'jumlah'       => $jumlah,
                        'bulan'        => $bt['bulan'],
                        'tahun'        => $bt['tahun'],
                        'keterangan'   => $this->keteranganPengeluaran($pos->nama, $bt),
                    ]);
                }
            }
        }
    }

    private function keteranganPengeluaran(string $namaPos, array $bt): string
    {
        $bulanNama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return "{$namaPos} bulan {$bulanNama[$bt['bulan']]} {$bt['tahun']}";
    }

    // =========================================================
    // Reset Sequence (PostgreSQL)
    // =========================================================

    private function resetSequences(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $sequences = [
            'tahun_ajaran_id_seq'        => 'tahun_ajaran',
            'siswa_id_seq'               => 'siswa',
            'siswa_tahun_ajaran_id_seq'  => 'siswa_tahun_ajaran',
            'master_tarif_spp_id_seq'    => 'master_tarif_spp',
            'jenis_penerimaan_id_seq'    => 'jenis_penerimaan',
            'tagihan_spp_id_seq'         => 'tagihan_spp',
            'tagihan_iuran_id_seq'       => 'tagihan_iuran',
            'transaksi_id_seq'           => 'transaksi',
            'transaksi_detail_id_seq'    => 'transaksi_detail',
            'pos_biaya_id_seq'           => 'pos_biaya',
            'pengeluaran_id_seq'         => 'pengeluaran',
            'saldo_awal_id_seq'          => 'saldo_awal',
            'users_id_seq'               => 'users',
        ];

        foreach ($sequences as $seq => $table) {
            DB::statement("SELECT setval('public.{$seq}', (SELECT MAX(id) FROM public.{$table}))");
        }

        $this->command->info('Sequence PostgreSQL berhasil di-reset.');
    }

    // =========================================================
    // Summary
    // =========================================================

    private function printSummary(): void
    {
        $tables = [
            'sekolah', 'users', 'tahun_ajaran',
            'master_tarif_spp', 'jenis_penerimaan',
            'pos_biaya', 'saldo_awal',
            'siswa', 'siswa_tahun_ajaran',
            'tagihan_spp', 'tagihan_iuran',
            'transaksi', 'transaksi_detail',
            'pengeluaran',
        ];

        $this->command->newLine();
        $this->command->info('=== Ringkasan Data Dummy ===');

        foreach ($tables as $table) {
            $count = DB::table($table)->count();
            $this->command->line(sprintf('  %-25s : %s records', $table, number_format($count)));
        }

        // Statistik tagihan
        $sppLunas  = TagihanSpp::where('status', TagihanSpp::STATUS_LUNAS)->count();
        $sppBelum  = TagihanSpp::where('status', TagihanSpp::STATUS_BELUM)->count();
        $iuranLunas = TagihanIuran::where('status', TagihanIuran::STATUS_LUNAS)->count();
        $iuranBelum = TagihanIuran::where('status', TagihanIuran::STATUS_BELUM)->count();

        $this->command->newLine();
        $this->command->info('=== Statistik Tagihan ===');
        $this->command->line("  SPP Lunas   : {$sppLunas}");
        $this->command->line("  SPP Belum   : {$sppBelum}");
        $this->command->line("  Iuran Lunas : {$iuranLunas}");
        $this->command->line("  Iuran Belum : {$iuranBelum}");

        $this->command->newLine();
        $this->command->info('=== Kredensial Login ===');
        $this->command->line('  admin    / admin123  (role: admin)');
        $this->command->line('  bendahara / admin123  (role: bendahara)');
        $this->command->line('  staff_tu  / admin123  (role: staff_tu)');
        $this->command->newLine();
    }
}
