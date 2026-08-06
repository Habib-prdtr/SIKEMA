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
        'jenis_penerimaan_id',
        'tipe_potongan',
        'nilai_potongan',
        'keterangan',
    ];

    protected $casts = [
        'jenis_penerimaan_id' => 'integer',
        'nilai_potongan' => 'integer',
        'created_at' => 'datetime',
    ];

    // =========================================================
    // Relasi
    // =========================================================

    public function jenisPenerimaan()
    {
        return $this->belongsTo(JenisPenerimaan::class);
    }

    public function siswaDispensasi()
    {
        return $this->hasMany(SiswaDispensasi::class);
    }

    public function siswaTahunAjaran()
    {
        return $this->belongsToMany(SiswaTahunAjaran::class, 'siswa_dispensasi', 'dispensasi_id', 'siswa_tahun_ajaran_id')
            ->withPivot(['durasi_dispensasi', 'semester_dispensasi', 'durasi_ganjil', 'durasi_genap'])
            ->withTimestamps();
    }

    // Accessor
    public function getTargetNamaAttribute(): string
    {
        if ($this->jenis_penerimaan_id && $this->jenisPenerimaan) {
            $tahunNama = $this->jenisPenerimaan->tahunAjaran->nama ?? null;
            return $this->jenisPenerimaan->nama . ($tahunNama ? " ({$tahunNama})" : '');
        }
        return 'SPP';
    }
}
