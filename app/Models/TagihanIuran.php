<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanIuran extends Model
{
    use HasFactory;

    public const STATUS_BELUM = 'belum';
    public const STATUS_CICILAN = 'cicilan';
    public const STATUS_LUNAS = 'lunas';

    public $timestamps = false;

    protected $table = 'tagihan_iuran';

    protected $fillable = [
        'siswa_tahun_ajaran_id',
        'jenis_penerimaan_id',
        'tagihan',
        'terbayar',
        'status',
    ];

    protected $casts = [
        'tagihan' => 'integer',
        'terbayar' => 'integer',
        'updated_at' => 'datetime',
    ];

    // =========================================================
    // Relasi
    // =========================================================

    public function siswaTahunAjaran()
    {
        return $this->belongsTo(SiswaTahunAjaran::class);
    }

    public function jenisPenerimaan()
    {
        return $this->belongsTo(JenisPenerimaan::class);
    }

    // =========================================================
    // Helper
    // =========================================================

    /**
     * Hitung sisa tagihan yang belum terbayar.
     */
    public function sisa(): int
    {
        return $this->tagihan - $this->terbayar;
    }

    /**
     * Rekam pembayaran dan update status.
     */
    public function bayar(int $nominal): void
    {
        $this->terbayar += $nominal;
        $this->status = $this->terbayar >= $this->tagihan ? self::STATUS_LUNAS : self::STATUS_CICILAN;
        $this->save();
    }
}
