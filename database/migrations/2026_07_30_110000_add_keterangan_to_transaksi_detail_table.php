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
        if (!Schema::hasColumn('transaksi_detail', 'keterangan')) {
            Schema::table('transaksi_detail', function (Blueprint $table) {
                $table->string('keterangan')->nullable();
            });
        }

        // Drop check constraint for PostgreSQL / MySQL if enum constraint exists
        try {
            if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE transaksi_detail DROP CONSTRAINT IF EXISTS transaksi_detail_jenis_check;');
            } elseif (in_array(\Illuminate\Support\Facades\DB::getDriverName(), ['mysql', 'mariadb'])) {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE transaksi_detail DROP CONSTRAINT IF EXISTS transaksi_detail_jenis_check;');
            }
        } catch (\Throwable $e) {
            // Ignore if constraint does not exist
        }

        // Change jenis column to string if supported
        try {
            Schema::table('transaksi_detail', function (Blueprint $table) {
                $table->string('jenis')->change();
            });
        } catch (\Throwable $e) {
            // Ignore if change is not supported directly without doctrine/dbal
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('transaksi_detail', 'keterangan')) {
            Schema::table('transaksi_detail', function (Blueprint $table) {
                $table->dropColumn('keterangan');
            });
        }
    }
};
