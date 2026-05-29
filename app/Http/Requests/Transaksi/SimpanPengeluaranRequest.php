<?php

namespace App\Http\Requests\Transaksi;

use Illuminate\Foundation\Http\FormRequest;

class SimpanPengeluaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pos_biaya_id' => ['required', 'exists:pos_biaya,id'],
            'tanggal'      => ['required', 'date'],
            'jumlah'       => ['required', 'integer', 'min:1'],
            'keterangan'   => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'pos_biaya_id.required' => 'Pos biaya wajib dipilih.',
            'pos_biaya_id.exists'   => 'Pos biaya tidak ditemukan.',
            'tanggal.required'      => 'Tanggal wajib diisi.',
            'jumlah.required'       => 'Jumlah pengeluaran wajib diisi.',
            'jumlah.integer'        => 'Jumlah harus berupa angka bulat.',
            'jumlah.min'            => 'Jumlah minimal Rp 1.',
        ];
    }
}
