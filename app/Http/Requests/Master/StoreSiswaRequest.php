<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'no_induk'      => ['required', 'string', 'max:20', 'unique:siswa,no_induk'],
            'nama'          => ['required', 'string', 'max:100'],
            'kelas'         => ['required', 'string', 'max:10'],
            'asrama'        => ['nullable', 'string', 'max:50'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tanggal_masuk' => ['nullable', 'date'],
            'status'        => ['required', 'in:aktif,nonaktif,lulus'],
        ];
    }

    public function messages(): array
    {
        return [
            'no_induk.required'      => 'Nomor induk wajib diisi.',
            'no_induk.unique'        => 'Nomor induk sudah terdaftar.',
            'no_induk.max'           => 'Nomor induk maksimal 20 karakter.',
            'nama.required'          => 'Nama siswa wajib diisi.',
            'kelas.required'         => 'Kelas wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in'       => 'Jenis kelamin harus L atau P.',
            'status.required'        => 'Status wajib dipilih.',
            'status.in'              => 'Status harus aktif, nonaktif, atau lulus.',
        ];
    }
}
