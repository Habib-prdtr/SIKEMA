<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    public $timestamps = false;

    protected $table = 'sekolah';

    protected $fillable = [
        'nama_sekolah',
        'nama_yayasan',
        'alamat',
        'telepon',
        'email',
        'kepala_tu',
        'nip_kepala_tu',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    // =========================================================
    // Helper
    // =========================================================

    /**
     * Ambil data sekolah (selalu 1 baris).
     */
    public static function getData(): ?self
    {
        return static::first();
    }
}
