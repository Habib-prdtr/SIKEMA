<?php

namespace App\Http\Requests\Pengaturan;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSekolahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_sekolah'  => ['required', 'string', 'max:150'],
            'nama_yayasan'  => ['nullable', 'string', 'max:150'],
            'alamat'        => ['nullable', 'string'],
            'telepon'       => ['nullable', 'string', 'max:20'],
            'email'         => ['nullable', 'email', 'max:100'],
            'kepala_tu'     => ['nullable', 'string', 'max:100'],
            'nip_kepala_tu' => ['nullable', 'string', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_sekolah.required' => 'Nama sekolah wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
        ];
    }
}
