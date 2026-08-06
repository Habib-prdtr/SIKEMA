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
        Schema::table('dispensasi', function (Blueprint $table) {
            $table->unsignedSmallInteger('jenis_penerimaan_id')->nullable()->after('nama');

            $table->foreign('jenis_penerimaan_id')
                ->references('id')
                ->on('jenis_penerimaan')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispensasi', function (Blueprint $table) {
            $table->dropForeign(['jenis_penerimaan_id']);
            $table->dropColumn('jenis_penerimaan_id');
        });
    }
};
