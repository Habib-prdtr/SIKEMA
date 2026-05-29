<?php

namespace App\Http\Requests\Transaksi;

use App\Models\SiswaTahunAjaran;
use App\Models\TagihanIuran;
use App\Models\TagihanSpp;
use App\Services\TunggakanService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class SimpanPenerimaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'siswa_tahun_ajaran_id' => ['required', 'exists:siswa_tahun_ajaran,id'],
            'tanggal'               => ['required', 'date'],
            'keterangan'            => ['nullable', 'string', 'max:500'],
            'items'                 => ['required', 'array', 'min:1'],
            'items.*.jenis'         => ['required', 'in:spp,iuran,tunggakan'],
            'items.*.nominal'       => ['required', 'integer', 'min:1'],
            // Untuk SPP: bulan + tahun wajib
            'items.*.bulan'         => ['nullable', 'integer', 'min:1', 'max:12'],
            'items.*.tahun'         => ['nullable', 'integer'],
            // Untuk iuran: jenis_penerimaan_id wajib
            'items.*.jenis_penerimaan_id' => ['nullable', 'exists:jenis_penerimaan,id'],
            // Untuk iuran: tagihan_iuran_id wajib
            'items.*.tagihan_iuran_id'    => ['nullable', 'exists:tagihan_iuran,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'siswa_tahun_ajaran_id.required' => 'Siswa wajib dipilih.',
            'siswa_tahun_ajaran_id.exists'   => 'Data siswa-tahun ajaran tidak ditemukan.',
            'tanggal.required'               => 'Tanggal transaksi wajib diisi.',
            'items.required'                 => 'Minimal satu item pembayaran harus dipilih.',
            'items.min'                      => 'Minimal satu item pembayaran harus dipilih.',
            'items.*.jenis.required'         => 'Jenis item wajib diisi.',
            'items.*.jenis.in'              => 'Jenis item tidak valid.',
            'items.*.nominal.required'       => 'Nominal wajib diisi.',
            'items.*.nominal.min'            => 'Nominal minimal Rp 1.',
        ];
    }

    /**
     * Validasi tambahan: nominal per item tidak boleh melebihi sisa tagihan.
     */
    protected function passedValidation(): void
    {
        $sta              = SiswaTahunAjaran::find($this->siswa_tahun_ajaran_id);
        $tunggakanService = app(TunggakanService::class);
        $errors           = [];

        foreach ($this->items as $index => $item) {
            $jenis   = $item['jenis'];
            $nominal = (int) ($item['nominal'] ?? 0);

            if ($jenis === 'spp') {
                $tagihan = TagihanSpp::where('siswa_tahun_ajaran_id', $sta->id)
                    ->where('bulan', $item['bulan'])
                    ->where('tahun', $item['tahun'])
                    ->first();

                if ($tagihan && $nominal > $tagihan->sisa()) {
                    $errors["items.{$index}.nominal"] = [
                        'Nominal melebihi sisa tagihan SPP bulan ini (Rp ' .
                        number_format($tagihan->sisa(), 0, ',', '.') . ').',
                    ];
                }
            } elseif ($jenis === 'iuran') {
                $tagihan = TagihanIuran::find($item['tagihan_iuran_id'] ?? null);

                if ($tagihan && $nominal > $tagihan->sisa()) {
                    $errors["items.{$index}.nominal"] = [
                        'Nominal melebihi sisa tagihan iuran (Rp ' .
                        number_format($tagihan->sisa(), 0, ',', '.') . ').',
                    ];
                }
            } elseif ($jenis === 'tunggakan') {
                $sisaTunggakan = $tunggakanService->hitungSisa($sta);

                if ($nominal > $sisaTunggakan) {
                    $errors["items.{$index}.nominal"] = [
                        'Nominal melebihi sisa tunggakan (Rp ' .
                        number_format($sisaTunggakan, 0, ',', '.') . ').',
                    ];
                }
            }
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }
}
