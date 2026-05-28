<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoAwal extends Model
{
    protected $table = 'saldo_awal';

    protected $fillable = [
        'tahun_ajaran_id',
        'jumlah',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    // =========================================================
    // Relasi
    // =========================================================

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
