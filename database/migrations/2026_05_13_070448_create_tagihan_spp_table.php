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
        Schema::create('tagihan_spp', function (Blueprint $table) {
            $table->id();

            $table->foreignId('siswa_tahun_ajaran_id')
                ->constrained('siswa_tahun_ajaran')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Bulan tagihan (1 = Januari, 12 = Desember)
            $table->smallInteger('bulan')->unsigned();

            $table->smallInteger('tahun')->unsigned();

            // Nominal tagihan dari tarif_spp siswa (BIGINT)
            $table->bigInteger('tagihan');

            // Akumulasi yang sudah dibayar (BIGINT)
            $table->bigInteger('terbayar')->default(0);

            $table->enum('status', [
                'belum',    // belum ada pembayaran
                'cicilan',  // sudah dibayar sebagian
                'lunas',    // terbayar = tagihan
            ])->default('belum');

            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_spp');
    }
};