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
        Schema::create('transaksi_detail', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaksi_id')
                ->constrained('transaksi')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Jenis item: spp | iuran | tunggakan
            $table->enum('jenis', ['spp', 'iuran', 'tunggakan']);

            // Diisi jika jenis = iuran; NULL jika spp atau tunggakan
            $table->foreignId('jenis_penerimaan_id')
                ->nullable()
                ->constrained('jenis_penerimaan')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Diisi jika jenis = spp (1-12); NULL untuk iuran/tunggakan
            $table->smallInteger('bulan')->unsigned()->nullable();

            // Diisi jika jenis = spp; NULL untuk iuran/tunggakan
            $table->smallInteger('tahun')->unsigned()->nullable();

            // Nominal yang dibayar untuk item ini (BIGINT)
            $table->bigInteger('nominal');

            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_detail');
    }
};
