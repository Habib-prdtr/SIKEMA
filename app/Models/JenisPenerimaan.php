<?php

namespace App\Models;

use App\Traits\HasHashids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPenerimaan extends Model
{
    use HasFactory, HasHashids;

    public $timestamps = false;

    protected $table = 'jenis_penerimaan';

    protected $fillable = [
        'tahun_ajaran_id',
        'urutan',
        'nama',
        'kelas',
        'tarif',
        'is_aktif',
    ];

    protected $casts = [
        'tarif' => 'integer',
        'is_aktif' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Cek apakah iuran ini berlaku untuk kelas siswa tertentu.
     */
    public function matchesKelas(?string $siswaKelas): bool
    {
        if (empty($this->kelas) || in_array(strtolower(trim($this->kelas)), ['semua kelas', 'semua'])) {
            return true;
        }

        if (empty($siswaKelas)) {
            return false;
        }

        // 1. Exact match (misal: "7A" === "7A" atau "Kelas 7" === "Kelas 7")
        if (strcasecmp(trim($this->kelas), trim($siswaKelas)) === 0) {
            return true;
        }

        // 2. Grade level match (misal: jenis_penerimaan.kelas = "Kelas 7" atau "7", siswa.kelas = "7A" atau "7B")
        $jpGrade = $this->getGradeFromKelas($this->kelas);
        $siswaGrade = $this->getGradeFromKelas($siswaKelas);

        if ($jpGrade !== null && $siswaGrade !== null && $jpGrade === $siswaGrade) {
            $cleanedJpKelas = str_replace(['kelas', 'Kelas', 'KELAS', ' '], '', $this->kelas);
            $jpHasSubClass = (bool) preg_match('/[a-zA-Z]/', $cleanedJpKelas);
            if (! $jpHasSubClass) {
                return true;
            }
        }

        return false;
    }

    private function getGradeFromKelas(?string $kelas): ?int
    {
        if (! $kelas) return null;

        if (preg_match('/\d+/', $kelas, $matches)) {
            return (int) $matches[0];
        }

        $romanMap = [
            'viii' => 8, 'vii' => 7, 'xii' => 12, 'iii' => 3,
            'xi' => 11, 'ix' => 9, 'vi' => 6, 'ii' => 2,
            'iv' => 4, 'x' => 10, 'v' => 5, 'i' => 1,
        ];

        $normalized = strtolower($kelas);
        foreach ($romanMap as $roman => $num) {
            if (strpos($normalized, $roman) !== false) {
                return $num;
            }
        }

        return null;
    }

    // =========================================================
    // Relasi
    // =========================================================

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function tagihanIuran()
    {
        return $this->hasMany(TagihanIuran::class);
    }

    public function transaksiDetail()
    {
        return $this->hasMany(TransaksiDetail::class);
    }
}
