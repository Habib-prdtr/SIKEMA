<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    public $timestamps = false;

    protected $table = 'transaksi_detail';

    protected $fillable = [
        'transaksi_id',
        'jenis',
        'jenis_penerimaan_id',
        'bulan',
        'tahun',
        'nominal',
    ];

    protected $casts = [
        'nominal'    => 'integer',
        'bulan'      => 'integer',
        'tahun'      => 'integer',
        'created_at' => 'datetime',
    ];

    // =========================================================
    // Relasi
    // =========================================================

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function jenisPenerimaan()
    {
        return $this->belongsTo(JenisPenerimaan::class);
    }
}
