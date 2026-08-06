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
        'dispensasi_id',
        'durasi_dispensasi',
        'tunggakan_awal',
    ];

    protected $casts = [
        'tarif_spp' => 'integer',
        'dispensasi_id' => 'integer',
        'durasi_dispensasi' => 'integer',
        'tunggakan_awal' => 'integer',
        'created_at' => 'datetime',
    ];

    // =========================================================
    // Relasi
    // =========================================================

    public function siswaDispensasi()
    {
        return $this->hasMany(SiswaDispensasi::class, 'siswa_tahun_ajaran_id');
    }

    public function dispensasiList()
    {
        return $this->belongsToMany(Dispensasi::class, 'siswa_dispensasi', 'siswa_tahun_ajaran_id', 'dispensasi_id')
            ->withPivot(['durasi_dispensasi', 'semester_dispensasi', 'durasi_ganjil', 'durasi_genap'])
            ->withTimestamps();
    }

    public function dispensasi()
    {
        return $this->belongsTo(Dispensasi::class);
    }

    /**
     * Dapatkan objek SiswaDispensasi yang aktif untuk SPP (jenis_penerimaan_id null).
     */
    public function getSppDispensasi(): ?SiswaDispensasi
    {
        return $this->siswaDispensasi
            ->first(function ($sd) {
                return $sd->dispensasi && empty($sd->dispensasi->jenis_penerimaan_id);
            });
    }

    /**
     * Dapatkan objek SiswaDispensasi yang aktif untuk jenis_penerimaan_id tertentu.
     */
    public function getIuranDispensasi(int $jenisPenerimaanId): ?SiswaDispensasi
    {
        return $this->siswaDispensasi
            ->first(function ($sd) use ($jenisPenerimaanId) {
                return $sd->dispensasi && $sd->dispensasi->jenis_penerimaan_id == $jenisPenerimaanId;
            });
    }

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

    public function tagihanTabunganWajib()
    {
        return $this->hasMany(TagihanTabunganWajib::class);
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
