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
        // Menggantikan jenis_pembayaran — disesuaikan dengan PRD
        Schema::create('jenis_penerimaan', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajaran')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Urutan tampil di form (1-15 sesuai PRD, max 15 item iuran)
            $table->smallInteger('urutan')->unsigned();

            $table->string('nama', 100);

            // Nominal per siswa (BIGINT, bukan float)
            $table->bigInteger('tarif');

            $table->boolean('is_aktif')->default(true);

            $table->timestamp('created_at')->nullable();

            $table->unique(['tahun_ajaran_id', 'urutan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_penerimaan');
    }
};
