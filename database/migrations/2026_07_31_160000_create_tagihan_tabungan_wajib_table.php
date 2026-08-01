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
        Schema::create('tagihan_tabungan_wajib', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_tahun_ajaran_id')
                ->constrained('siswa_tahun_ajaran')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            
            $table->unsignedTinyInteger('bulan'); // 1-12
            $table->smallInteger('tahun');        // e.g. 2024
            
            $table->bigInteger('tagihan');
            $table->bigInteger('terbayar')->default(0);
            
            $table->string('status', 20)->default('belum'); // belum | cicilan | lunas
            $table->timestamp('updated_at')->nullable();
            
            $table->unique(['siswa_tahun_ajaran_id', 'bulan', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_tabungan_wajib');
    }
};
