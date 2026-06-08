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
        Schema::create('master_tarif_spp', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajaran')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            
            // Nama kelas / tingkat (misal: "Kelas 7", "Kelas 8", "Kelas 9")
            $table->string('kelas', 50);
            
            // Nominal tarif per bulan
            $table->bigInteger('tarif');
            
            $table->unique(['tahun_ajaran_id', 'kelas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_tarif_spp');
    }
};
