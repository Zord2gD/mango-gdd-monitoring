<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreKebunRequest extends FormRequest
{
    /**
     * Hanya petani yang boleh membuat kebun.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'petani';
    }

    /**
     * Aturan validasi untuk membuat kebun baru.
     */
    public function rules(): array
    {
        return [
            'nama_kebun'   => 'required|string|max:255',
            'lokasi'       => 'required|string|max:255',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
            'jumlah_pohon' => 'required|integer|min:1|max:99999',
            'jenis_mangga' => 'required|string|max:100',
        ];
    }

    /**
     * Pesan error yang lebih ramah dan mudah dipahami.
     */
    public function messages(): array
    {
        return [
            'nama_kebun.required'   => 'Nama kebun wajib diisi.',
            'nama_kebun.max'        => 'Nama kebun maksimal 255 karakter.',
            'lokasi.required'       => 'Lokasi kebun wajib diisi.',
            'latitude.numeric'      => 'Latitude harus berupa angka desimal.',
            'latitude.between'      => 'Latitude harus antara -90 dan 90.',
            'longitude.numeric'     => 'Longitude harus berupa angka desimal.',
            'longitude.between'     => 'Longitude harus antara -180 dan 180.',
            'jumlah_pohon.required' => 'Jumlah pohon wajib diisi.',
            'jumlah_pohon.integer'  => 'Jumlah pohon harus berupa angka bulat.',
            'jumlah_pohon.min'      => 'Jumlah pohon minimal 1.',
            'jumlah_pohon.max'      => 'Jumlah pohon tidak boleh lebih dari 99.999.',
            'jenis_mangga.required' => 'Varietas mangga wajib dipilih.',
        ];
    }
}
