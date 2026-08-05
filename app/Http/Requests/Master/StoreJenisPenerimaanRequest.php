<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class StoreJenisPenerimaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $tahunAjaranId = $this->input('tahun_ajaran_id');
        $urutan = $this->input('urutan');
        if (empty($urutan) && $tahunAjaranId) {
            $maxUrutan = \App\Models\JenisPenerimaan::where('tahun_ajaran_id', $tahunAjaranId)->max('urutan') ?? 0;
            $urutan = $maxUrutan + 1;
        }

        $this->merge([
            'is_aktif' => $this->has('is_aktif'),
            'urutan' => $urutan ?? 1,
        ]);
    }

    public function rules(): array
    {
        return [
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajaran,id'],
            'urutan' => ['nullable', 'integer', 'min:1'],
            'nama' => ['required', 'string', 'max:100'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'tarif' => ['required', 'integer', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'is_aktif' => ['boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $tahunAjaranId = $this->input('tahun_ajaran_id');
            if ($tahunAjaranId) {
                $count = \App\Models\JenisPenerimaan::where('tahun_ajaran_id', $tahunAjaranId)->count();
                if ($count >= 15) {
                    $validator->errors()->add('nama', 'Batas maksimal penyimpanan jenis iuran yang diperbolehkan di madrasah adalah 15 jenis iuran per tahun ajaran. Penambahan jenis iuran ke-16 ditolak.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'urutan.required' => 'Urutan wajib diisi.',
            'urutan.min' => 'Urutan minimal 1.',
            'urutan.max' => 'Urutan maksimal 15.',
            'nama.required' => 'Nama iuran wajib diisi.',
            'tarif.required' => 'Tarif wajib diisi.',
            'tarif.integer' => 'Tarif harus berupa angka bulat.',
            'tarif.min' => 'Tarif tidak boleh negatif.',
        ];
    }
}
