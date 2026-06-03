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
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();

            $table->string('no_induk', 20)->unique();

            $table->string('nama', 100);

            $table->string('kelas', 10);

            // Asrama (opsional, contoh: Putra A, Putri B)
            $table->string('asrama', 50)->nullable();

            $table->enum('jenis_kelamin', ['L', 'P']);

            $table->date('tanggal_masuk')->nullable();

            $table->enum('status', [
                'aktif',
                'nonaktif',
                'lulus',
            ])->default('aktif');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
