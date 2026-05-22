<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreSuhuRequest extends FormRequest
{
    /**
     * Hanya petani yang boleh input suhu harian.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'petani';
    }

    /**
     * Aturan validasi untuk input data suhu harian.
     */
    public function rules(): array
    {
        return [
            'kebun_id' => 'required|exists:kebuns,id',
            'tanggal'  => 'required|date|before_or_equal:today',
            'tmax'     => 'required|numeric|min:-10|max:60',
            'tmin'     => 'required|numeric|min:-10|max:60|lte:tmax',
        ];
    }

    /**
     * Pesan error yang ramah dan mudah dipahami petani.
     */
    public function messages(): array
    {
        return [
            'kebun_id.required'    => 'Pilih kebun terlebih dahulu.',
            'kebun_id.exists'      => 'Kebun yang dipilih tidak ditemukan.',
            'tanggal.required'     => 'Tanggal pengukuran wajib diisi.',
            'tanggal.date'         => 'Format tanggal tidak valid.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh di masa depan.',
            'tmax.required'        => 'Suhu maksimum (Tmax) wajib diisi.',
            'tmax.numeric'         => 'Tmax harus berupa angka.',
            'tmax.min'             => 'Tmax tidak boleh kurang dari -10°C.',
            'tmax.max'             => 'Tmax tidak boleh lebih dari 60°C.',
            'tmin.required'        => 'Suhu minimum (Tmin) wajib diisi.',
            'tmin.numeric'         => 'Tmin harus berupa angka.',
            'tmin.min'             => 'Tmin tidak boleh kurang dari -10°C.',
            'tmin.max'             => 'Tmin tidak boleh lebih dari 60°C.',
            'tmin.lte'             => 'Tmin tidak boleh lebih besar dari Tmax.',
        ];
    }
}
