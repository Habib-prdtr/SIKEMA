<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePosBiayaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama'       => ['required', 'string', 'max:100'],
            'anggaran'   => ['required', 'integer', 'min:0'],
            'keterangan' => ['nullable', 'string'],
            'is_aktif'   => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'     => 'Nama pos biaya wajib diisi.',
            'anggaran.required' => 'Anggaran wajib diisi.',
            'anggaran.integer'  => 'Anggaran harus berupa angka bulat.',
            'anggaran.min'      => 'Anggaran tidak boleh negatif.',
        ];
    }
}
