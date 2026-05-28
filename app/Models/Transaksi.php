<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    public $timestamps = false;

    protected $table = 'transaksi';

    protected $fillable = [
        'no_transaksi',
        'siswa_tahun_ajaran_id',
        'user_id',
        'tanggal',
        'total_bayar',
        'keterangan',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'total_bayar' => 'integer',
        'created_at' => 'datetime',
    ];

    // =========================================================
    // Relasi
    // =========================================================

    public function siswaTahunAjaran()
    {
        return $this->belongsTo(SiswaTahunAjaran::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detail()
    {
        return $this->hasMany(TransaksiDetail::class);
    }
}
