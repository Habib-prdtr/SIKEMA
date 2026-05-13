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
        Schema::create('pos_biaya', function (Blueprint $table) {
            $table->id();

            $table->string('nama', 100);

            $table->bigInteger('anggaran');

            $table->string('tahun_pelajaran', 9);

            $table->text('keterangan')->nullable();

            $table->boolean('is_aktif')->default(1);

            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_biaya');
    }
};