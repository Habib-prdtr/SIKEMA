<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagihanIuran extends Model
{
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
        'tagihan'    => 'integer',
        'terbayar'   => 'integer',
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
        $this->status   = $this->terbayar >= $this->tagihan ? 'lunas' : 'cicilan';
        $this->save();
    }
}
