<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class StoreAllSiswaTahunAjaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajaran,id'],
            'master_tarif_spp_id' => ['required', 'exists:master_tarif_spp,id'],
            'kelas' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'tahun_ajaran_id.exists' => 'Tahun ajaran tidak ditemukan.',
            'master_tarif_spp_id.required' => 'Tarif SPP wajib dipilih.',
            'master_tarif_spp_id.exists' => 'Tarif SPP tidak ditemukan.',
            'kelas.required' => 'Kelas wajib dipilih.',
        ];
    }
}
