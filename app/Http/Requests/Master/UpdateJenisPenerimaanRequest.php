<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJenisPenerimaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_aktif' => $this->has('is_aktif'),
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'tarif' => ['required', 'integer', 'min:0'],
            'urutan' => ['nullable', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'is_aktif' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama iuran wajib diisi.',
            'tarif.required' => 'Tarif wajib diisi.',
            'tarif.integer' => 'Tarif harus berupa angka bulat.',
            'tarif.min' => 'Tarif tidak boleh negatif.',
        ];
    }
}
