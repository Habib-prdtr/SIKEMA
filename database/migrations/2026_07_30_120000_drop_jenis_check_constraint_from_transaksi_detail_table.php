<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('ALTER TABLE transaksi_detail DROP CONSTRAINT IF EXISTS transaksi_detail_jenis_check;');
            } elseif (in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
                DB::statement('ALTER TABLE transaksi_detail DROP CONSTRAINT IF EXISTS transaksi_detail_jenis_check;');
            }
        } catch (\Throwable $e) {
            // Ignore if constraint does not exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
