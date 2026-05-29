<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaldoAwalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajaran,id', 'unique:saldo_awal,tahun_ajaran_id'],
            'jumlah'          => ['required', 'integer', 'min:0'],
            'keterangan'      => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'tahun_ajaran_id.unique'   => 'Saldo awal untuk tahun ajaran ini sudah ada.',
            'jumlah.required'          => 'Jumlah saldo awal wajib diisi.',
            'jumlah.integer'           => 'Jumlah harus berupa angka bulat.',
            'jumlah.min'               => 'Jumlah tidak boleh negatif.',
        ];
    }
}
