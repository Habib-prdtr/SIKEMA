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
            'tarif_spp' => ['required', 'integer', 'min:0'],
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
            'tarif_spp.required' => 'Tarif SPP wajib diisi.',
            'tarif_spp.integer' => 'Tarif SPP harus berupa angka bulat.',
            'tarif_spp.min' => 'Tarif SPP tidak boleh negatif.',
            'tunggakan_awal.integer' => 'Tunggakan awal harus berupa angka bulat.',
            'tunggakan_awal.min' => 'Tunggakan awal tidak boleh negatif.',
        ];
    }
}
