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
        Schema::create('tagihan_iuran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('jenis_pembayaran_id')
                ->constrained('jenis_pembayaran')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('tahun_pelajaran', 9);

            $table->bigInteger('tagihan');

            $table->bigInteger('terbayar')->default(0);

            $table->enum('status', [
                'belum',
                'cicilan',
                'lunas'
            ])->default('belum');

            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_iuran');
    }
};