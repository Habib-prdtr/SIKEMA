<?php

namespace App\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait HasHashids
{
    /**
     * Mengubah primary key menjadi hashid untuk URL.
     */
    public function getRouteKey()
    {
        return Hashids::encode($this->getKey());
    }

    /**
     * Membaca hashid dari URL menjadi primary key.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Decode value. Jika hasilnya array kosong, set ke 0 agar tidak found.
        $decoded = Hashids::decode($value);
        $id = $decoded[0] ?? 0;

        return $this->where($field ?? $this->getRouteKeyName(), $id)->firstOrFail();
    }
}
