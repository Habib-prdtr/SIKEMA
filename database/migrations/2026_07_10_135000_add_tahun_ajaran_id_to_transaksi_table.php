<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->foreignId('tahun_ajaran_id')
                ->nullable()
                ->after('siswa_tahun_ajaran_id')
                ->constrained('tahun_ajaran')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        // Backfill existing records
        DB::statement('
            UPDATE transaksi 
            SET tahun_ajaran_id = (
                SELECT tahun_ajaran_id 
                FROM siswa_tahun_ajaran 
                WHERE siswa_tahun_ajaran.id = transaksi.siswa_tahun_ajaran_id
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->dropColumn('tahun_ajaran_id');
        });
    }
};
