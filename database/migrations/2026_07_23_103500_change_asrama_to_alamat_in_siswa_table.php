<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            if (Schema::hasColumn('siswa', 'asrama')) {
                $table->renameColumn('asrama', 'alamat');
            } elseif (!Schema::hasColumn('siswa', 'alamat')) {
                $table->text('alamat')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            if (Schema::hasColumn('siswa', 'alamat')) {
                $table->renameColumn('alamat', 'asrama');
            }
        });
    }
};
