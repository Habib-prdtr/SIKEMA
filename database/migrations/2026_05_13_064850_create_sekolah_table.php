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
        Schema::create('sekolah', function (Blueprint $table) {
            $table->tinyIncrements('id');

            $table->string('nama_sekolah', 150);
            $table->string('nama_yayasan', 150)->nullable();

            $table->text('alamat')->nullable();

            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable();

            $table->string('kepala_tu', 100)->nullable();
            $table->string('nip_kepala_tu', 30)->nullable();

            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sekolah');
    }
};
