<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPenerimaan extends Model
{
    public $timestamps = false;

    protected $table = 'jenis_penerimaan';

    protected $fillable = [
        'tahun_ajaran_id',
        'urutan',
        'nama',
        'tarif',
        'is_aktif',
    ];

    protected $casts = [
        'tarif' => 'integer',
        'is_aktif' => 'boolean',
        'created_at' => 'datetime',
    ];

    // =========================================================
    // Relasi
    // =========================================================

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function tagihanIuran()
    {
        return $this->hasMany(TagihanIuran::class);
    }

    public function transaksiDetail()
    {
        return $this->hasMany(TransaksiDetail::class);
    }
}
