<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class StoreTarifSppRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajaran,id'],
            'kelas' => ['required', 'string', 'max:50'],
            'tarif' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     */
    public function messages(): array
    {
        return [
            'kelas.required' => 'Nama/Tingkat Kelas wajib diisi.',
            'tarif.required' => 'Tarif wajib diisi.',
            'tarif.integer' => 'Tarif harus berupa angka.',
            'tarif.min' => 'Tarif tidak boleh negatif.',
        ];
    }
}
