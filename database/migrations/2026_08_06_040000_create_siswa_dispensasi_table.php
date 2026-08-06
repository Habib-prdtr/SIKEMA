<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('siswa_dispensasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_tahun_ajaran_id')->constrained('siswa_tahun_ajaran')->onDelete('cascade');
            $table->foreignId('dispensasi_id')->constrained('dispensasi')->onDelete('cascade');
            $table->unsignedSmallInteger('durasi_dispensasi')->default(12);
            $table->string('semester_dispensasi')->default('semua');
            $table->unsignedSmallInteger('durasi_ganjil')->default(0);
            $table->unsignedSmallInteger('durasi_genap')->default(0);
            $table->timestamps();

            $table->unique(['siswa_tahun_ajaran_id', 'dispensasi_id']);
        });

        // Migrate existing non-null dispensasi_id from siswa_tahun_ajaran to siswa_dispensasi
        $existing = DB::table('siswa_tahun_ajaran')
            ->whereNotNull('dispensasi_id')
            ->get();

        foreach ($existing as $row) {
            DB::table('siswa_dispensasi')->insertOrIgnore([
                'siswa_tahun_ajaran_id' => $row->id,
                'dispensasi_id' => $row->dispensasi_id,
                'durasi_dispensasi' => $row->durasi_dispensasi ?? 12,
                'semester_dispensasi' => 'semua',
                'durasi_ganjil' => 0,
                'durasi_genap' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_dispensasi');
    }
};
