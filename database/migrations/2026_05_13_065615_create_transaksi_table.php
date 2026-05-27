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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();

            // Format: TRX-0001, TRX-0042
            $table->string('no_transaksi', 20)->unique();

            // Siapa yang bayar (siswa di tahun ajaran mana)
            $table->foreignId('siswa_tahun_ajaran_id')
                ->constrained('siswa_tahun_ajaran')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Operator yang mencatat
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('tanggal');

            // Total semua item dalam sesi bayar ini (BIGINT)
            $table->bigInteger('total_bayar');

            $table->text('keterangan')->nullable();

            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};