<?php

namespace App\Models;

use App\Traits\HasHashids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory, HasHashids;

    public const STATUS_AKTIF = 'aktif';
    public const STATUS_LULUS = 'lulus';
    public const STATUS_PINDAH = 'pindah';
    public const STATUS_BERHENTI = 'berhenti';

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
