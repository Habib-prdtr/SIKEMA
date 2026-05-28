<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaTahunAjaran extends Model
{
    public $timestamps = false;

    protected $table = 'siswa_tahun_ajaran';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran_id',
        'tarif_spp',
        'tunggakan_awal',
    ];

    protected $casts = [
        'tarif_spp'      => 'integer',
        'tunggakan_awal' => 'integer',
        'created_at'     => 'datetime',
    ];

    // =========================================================
    // Relasi
    // =========================================================

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function tagihanSpp()
    {
        return $this->hasMany(TagihanSpp::class);
    }

    public function tagihanIuran()
    {
        return $this->hasMany(TagihanIuran::class);
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }
}
