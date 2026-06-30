<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiswaTahunAjaranTunggakanRequest extends FormRequest
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
            'tunggakan_awal' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     */
    public function messages(): array
    {
        return [
            'tunggakan_awal.required' => 'Tunggakan awal wajib diisi.',
            'tunggakan_awal.numeric' => 'Tunggakan awal harus berupa angka.',
            'tunggakan_awal.min' => 'Tunggakan awal tidak boleh negatif.',
        ];
    }
}
