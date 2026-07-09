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
        Schema::table('siswa_tahun_ajaran', function (Blueprint $table) {
            $table->unsignedSmallInteger('dispensasi_id')->nullable()->after('tarif_spp');
            $table->integer('durasi_dispensasi')->nullable()->after('dispensasi_id'); // Durasi bulanan (misal 1-12 bulan)

            $table->foreign('dispensasi_id')
                ->references('id')
                ->on('dispensasi')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa_tahun_ajaran', function (Blueprint $table) {
            $table->dropForeign(['dispensasi_id']);
            $table->dropColumn(['dispensasi_id', 'durasi_dispensasi']);
        });
    }
};
