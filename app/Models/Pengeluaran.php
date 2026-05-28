<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    public $timestamps = false;

    protected $table = 'pengeluaran';

    protected $fillable = [
        'pos_biaya_id',
        'user_id',
        'tanggal',
        'jumlah',
        'bulan',
        'tahun',
        'keterangan',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'jumlah'     => 'integer',
        'bulan'      => 'integer',
        'tahun'      => 'integer',
        'created_at' => 'datetime',
    ];

    // =========================================================
    // Relasi
    // =========================================================

    public function posBiaya()
    {
        return $this->belongsTo(PosBiaya::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
