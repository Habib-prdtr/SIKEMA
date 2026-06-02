<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosBiaya extends Model
{
    public $timestamps = false;

    protected $table = 'pos_biaya';

    protected $fillable = [
        'tahun_ajaran_id',
        'nama',
        'anggaran',
        'keterangan',
        'is_aktif',
    ];

    protected $casts = [
        'anggaran' => 'integer',
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

    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class);
    }

    // =========================================================
    // Helper
    // =========================================================

    /**
     * Hitung total realisasi pengeluaran di pos ini.
     */
    public function totalRealisasi(): int
    {
        return (int) $this->pengeluaran()->sum('jumlah');
    }

    /**
     * Hitung sisa anggaran pos ini.
     */
    public function sisaAnggaran(): int
    {
        return $this->anggaran - $this->totalRealisasi();
    }
}
