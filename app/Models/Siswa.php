<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';

    protected $fillable = [
        'no_induk',
        'nama',
        'kelas',
        'asrama',
        'jenis_kelamin',
        'tanggal_masuk',
        'status',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
    ];

    // =========================================================
    // Relasi
    // =========================================================

    /**
     * Riwayat aktifasi siswa di setiap tahun ajaran.
     */
    public function tahunAjaran()
    {
        return $this->hasMany(SiswaTahunAjaran::class);
    }

    // =========================================================
    // Helper
    // =========================================================

    /**
     * Ambil record siswa_tahun_ajaran untuk tahun ajaran aktif.
     */
    public function tahunAjaranAktif(): ?SiswaTahunAjaran
    {
        $tahunAktif = TahunAjaran::aktif();
        if (! $tahunAktif) {
            return null;
        }

        return $this->tahunAjaran()
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->first();
    }
}
