<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiswaTahunAjaranSppRequest extends FormRequest
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
            'master_tarif_spp_id' => ['required', 'exists:master_tarif_spp,id'],
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     */
    public function messages(): array
    {
        return [
            'master_tarif_spp_id.required' => 'Tarif SPP wajib dipilih.',
            'master_tarif_spp_id.exists' => 'Tarif SPP tidak ditemukan.',
        ];
    }
}
