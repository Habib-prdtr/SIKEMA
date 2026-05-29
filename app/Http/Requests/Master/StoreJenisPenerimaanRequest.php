<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class StoreJenisPenerimaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajaran,id'],
            'urutan'          => ['required', 'integer', 'min:1', 'max:15'],
            'nama'            => ['required', 'string', 'max:100'],
            'tarif'           => ['required', 'integer', 'min:0'],
            'is_aktif'        => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'urutan.required'          => 'Urutan wajib diisi.',
            'urutan.min'               => 'Urutan minimal 1.',
            'urutan.max'               => 'Maksimal 15 jenis iuran per tahun ajaran.',
            'nama.required'            => 'Nama iuran wajib diisi.',
            'tarif.required'           => 'Tarif wajib diisi.',
            'tarif.integer'            => 'Tarif harus berupa angka bulat.',
            'tarif.min'                => 'Tarif tidak boleh negatif.',
        ];
    }
}
