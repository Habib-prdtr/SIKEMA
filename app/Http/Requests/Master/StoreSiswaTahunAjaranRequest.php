<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiswaTahunAjaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'siswa_id' => ['required', 'exists:siswa,id'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajaran,id'],
            'master_tarif_spp_id' => ['required', 'exists:master_tarif_spp,id'],
            'tunggakan_awal' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'siswa_id.required' => 'Siswa wajib dipilih.',
            'siswa_id.exists' => 'Siswa tidak ditemukan.',
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'tahun_ajaran_id.exists' => 'Tahun ajaran tidak ditemukan.',
            'master_tarif_spp_id.required' => 'Tarif SPP wajib dipilih.',
            'master_tarif_spp_id.exists' => 'Tarif SPP tidak ditemukan.',
            'tunggakan_awal.integer' => 'Tunggakan awal harus berupa angka bulat.',
            'tunggakan_awal.min' => 'Tunggakan awal tidak boleh negatif.',
        ];
    }
}
