<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiwayatPanen;
use App\Models\Kebun;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RiwayatPanenController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            $riwayat = RiwayatPanen::with('kebun.user')->orderBy('tanggal_panen', 'desc')->get();
        } else {
            $riwayat = RiwayatPanen::whereHas('kebun', function ($q) {
                $q->where('user_id', Auth::id());
            })->with('kebun')->orderBy('tanggal_panen', 'desc')->get();
        }

        return view('riwayat.index', compact('riwayat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kebun_id' => 'required|exists:kebuns,id',
            'hasil_panen_kg' => 'required|numeric|min:1',
            'catatan' => 'nullable|string'
        ]);

        $kebun = Kebun::findOrFail($request->kebun_id);

        // Security check: Petani can only panen their own kebun
        if (Auth::user()->role !== 'admin' && $kebun->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$kebun->fase || !$kebun->fase->tanggal_berbunga) {
            return back()->with('error', 'Kebun belum memiliki siklus tanam aktif.');
        }

        // 1. Simpan Riwayat
        RiwayatPanen::create([
            'kebun_id' => $kebun->id,
            'tanggal_berbunga' => $kebun->fase->tanggal_berbunga,
            'tanggal_panen' => Carbon::today(),
            'total_gdd' => $kebun->total_gdd,
            'hasil_panen_kg' => $request->hasil_panen_kg,
            'catatan' => $request->catatan,
        ]);

        // 2. Reset Siklus Tanam (JANGAN HAPUS SUHU LAMA)
        // Set tanggal_berbunga = null, sehingga GDD untuk siklus ini akan kembali 0
        $kebun->fase->update([
            'tanggal_berbunga' => null
        ]);

        return redirect()->back()->with('success', 'Panen berhasil diselesaikan! Riwayat telah disimpan dan siklus kebun telah di-reset.');
    }
}
