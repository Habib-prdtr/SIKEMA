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
        Schema::create('jenis_pembayaran', function (Blueprint $table) {
            $table->tinyIncrements('id');

            $table->tinyInteger('urutan')->unsigned()->unique();

            $table->string('nama', 100);

            $table->bigInteger('tarif')->default(0);

            $table->boolean('is_flat')->default(1);

            $table->boolean('is_aktif')->default(1);

            $table->string('tahun_pelajaran', 9);

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_pembayaran');
    }
};