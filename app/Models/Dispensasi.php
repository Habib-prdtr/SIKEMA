<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispensasi extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'dispensasi';

    protected $fillable = [
        'nama',
        'tipe_potongan',
        'nilai_potongan',
        'keterangan',
    ];

    protected $casts = [
        'nilai_potongan' => 'integer',
        'created_at' => 'datetime',
    ];

    // =========================================================
    // Relasi
    // =========================================================

    public function siswaTahunAjaran()
    {
        return $this->hasMany(SiswaTahunAjaran::class);
    }
}
