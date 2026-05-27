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
        Schema::create('saldo_awal', function (Blueprint $table) {
            $table->smallIncrements('id');

            // Satu saldo awal per tahun ajaran
            $table->foreignId('tahun_ajaran_id')
                ->unique()
                ->constrained('tahun_ajaran')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Nominal kas awal (BIGINT)
            $table->bigInteger('jumlah');

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldo_awal');
    }
};