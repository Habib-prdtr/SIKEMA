<?php

namespace App\Models;

use App\Traits\HasHashids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterTarifSpp extends Model
{
    use HasFactory, HasHashids;

    public $timestamps = false;

    protected $table = 'master_tarif_spp';

    protected $fillable = [
        'tahun_ajaran_id',
        'kelas',
        'tarif',
    ];

    protected $casts = [
        'tarif' => 'integer',
    ];

    // =========================================================
    // Relasi
    // =========================================================

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
