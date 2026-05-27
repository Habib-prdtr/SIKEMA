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
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pos_biaya_id')
                ->constrained('pos_biaya')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('tanggal');

            // Nominal pengeluaran (BIGINT)
            $table->bigInteger('jumlah');

            // Bulan pengeluaran (1-12)
            $table->smallInteger('bulan')->unsigned();

            // Tahun pengeluaran
            $table->smallInteger('tahun')->unsigned();

            $table->text('keterangan')->nullable();

            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
    }
};