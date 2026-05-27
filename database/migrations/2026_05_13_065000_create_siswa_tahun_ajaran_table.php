<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('siswa_tahun_ajaran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajaran')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Tarif SPP per bulan untuk siswa ini di tahun ajaran ini
            $table->bigInteger('tarif_spp');

            // Sisa tunggakan dari tahun ajaran sebelumnya
            $table->bigInteger('tunggakan_awal')->default(0);

            $table->timestamp('created_at')->nullable();

            // Satu siswa hanya bisa diaktifkan sekali per tahun ajaran
            $table->unique(['siswa_id', 'tahun_ajaran_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_tahun_ajaran');
    }
};
