<?php

namespace App\Models;

use App\Traits\HasHashids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory, HasHashids;

    public $timestamps = false;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'nama',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
        'created_at' => 'datetime',
    ];

    // =========================================================
    // Relasi
    // =========================================================

    public function siswaTahunAjaran()
    {
        return $this->hasMany(SiswaTahunAjaran::class);
    }

    public function jenisPenerimaan()
    {
        return $this->hasMany(JenisPenerimaan::class);
    }

    public function posBiaya()
    {
        return $this->hasMany(PosBiaya::class);
    }

    public function saldoAwal()
    {
        return $this->hasOne(SaldoAwal::class);
    }

    // =========================================================
    // Helper
    // =========================================================

    /**
     * Ambil tahun ajaran yang sedang aktif.
     */
    public static function aktif(): ?self
    {
        return static::where('is_aktif', true)->first();
    }

    /**
     * Set tahun ajaran ini menjadi aktif (nonaktifkan yang lain).
     */
    public function setAktif(): void
    {
        static::where('is_aktif', true)->update(['is_aktif' => false]);
        $this->update(['is_aktif' => true]);
    }
}
