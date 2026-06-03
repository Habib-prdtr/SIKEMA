<?php

namespace App\Services;

use App\Models\SiswaTahunAjaran;

class TunggakanService
{
    /**
     * Hitung sisa tunggakan siswa untuk tahun ajaran ini.
     *
     * Rumus:
     *   sisa = tunggakan_awal - total cicilan tunggakan yang sudah masuk
     *
     * JANGAN duplikasi logika ini di Controller atau tempat lain.
     *
     * @return int Sisa tunggakan dalam rupiah (integer, tidak boleh negatif)
     */
    public function hitungSisa(SiswaTahunAjaran $sta): int
    {
        // Menggunakan property (bukan method) agar bisa memanfaatkan eager loading dari Controller
        $totalDibayar = $sta->transaksi
            ->flatMap(fn ($t) => $t->details)
            ->where('jenis', 'tunggakan')
            ->sum('nominal');

        $sisa = $sta->tunggakan_awal - (int) $totalDibayar;

        // Tidak boleh negatif (lebih bayar tidak mungkin terjadi karena validasi di form)
        return max(0, $sisa);
    }
}
