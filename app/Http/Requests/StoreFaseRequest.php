<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFaseRequest extends FormRequest
{
    /**
     * Hanya petani yang boleh membuat fase tanaman.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'petani';
    }

    /**
     * Aturan validasi untuk menyimpan fase tanaman (tanggal berbunga).
     */
    public function rules(): array
    {
        return [
            'kebun_id'         => 'required|exists:kebuns,id',
            'tanggal_berbunga' => 'required|date|before_or_equal:today',
        ];
    }

    /**
     * Pesan error yang ramah dan mudah dipahami pengguna.
     */
    public function messages(): array
    {
        return [
            'kebun_id.required'            => 'Pilih kebun terlebih dahulu.',
            'kebun_id.exists'              => 'Kebun yang dipilih tidak ditemukan.',
            'tanggal_berbunga.required'    => 'Tanggal berbunga wajib diisi.',
            'tanggal_berbunga.date'        => 'Format tanggal tidak valid.',
            'tanggal_berbunga.before_or_equal' => 'Tanggal berbunga tidak boleh di masa depan.',
        ];
    }
}
