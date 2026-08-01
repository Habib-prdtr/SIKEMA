<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    public $timestamps = false;

    protected $table = 'sekolah';

    protected $fillable = [
        'nama_sekolah',
        'nama_yayasan',
        'alamat',
        'telepon',
        'email',
        'kepala_tu',
        'nip_kepala_tu',
        'jenjang_kelas_aktif',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
    ];

    // =========================================================
    // Helper
    // =========================================================

    /**
     * Ambil data sekolah (selalu 1 baris).
     */
    public static function getData(): ?self
    {
        $sekolah = static::first();
        if (!$sekolah) {
            $sekolah = static::create([
                'nama_sekolah' => 'MTS IHYAUL ULUM',
                'jenjang_kelas_aktif' => 'semua',
            ]);
        }
        return $sekolah;
    }

    /**
     * Ambil jenjang kelas yang sedang aktif difokuskan.
     */
    public static function getJenjangAktif(): string
    {
        if (session()->has('jenjang_kelas_aktif')) {
            return (string) session('jenjang_kelas_aktif');
        }

        $sekolah = static::getData();
        $jenjang = $sekolah?->jenjang_kelas_aktif ?? 'semua';
        session(['jenjang_kelas_aktif' => $jenjang]);

        return $jenjang;
    }
}
