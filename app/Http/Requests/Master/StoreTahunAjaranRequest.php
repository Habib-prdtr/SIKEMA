<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class StoreTahunAjaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Format wajib: 2024/2025
            'nama' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/', 'unique:tahun_ajaran,nama'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama tahun ajaran wajib diisi.',
            'nama.regex'    => 'Format tahun ajaran harus 2024/2025.',
            'nama.unique'   => 'Tahun ajaran ini sudah ada.',
        ];
    }
}
