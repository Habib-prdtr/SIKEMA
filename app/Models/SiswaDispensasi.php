<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaDispensasi extends Model
{
    protected $table = 'siswa_dispensasi';

    protected $fillable = [
        'siswa_tahun_ajaran_id',
        'dispensasi_id',
        'durasi_dispensasi',
        'semester_dispensasi',
        'durasi_ganjil',
        'durasi_genap',
    ];

    protected $casts = [
        'siswa_tahun_ajaran_id' => 'integer',
        'dispensasi_id' => 'integer',
        'durasi_dispensasi' => 'integer',
        'durasi_ganjil' => 'integer',
        'durasi_genap' => 'integer',
    ];

    public function siswaTahunAjaran()
    {
        return $this->belongsTo(SiswaTahunAjaran::class);
    }

    public function dispensasi()
    {
        return $this->belongsTo(Dispensasi::class);
    }
}
