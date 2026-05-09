<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuhuHarian;
use App\Models\Kebun;
use Illuminate\Support\Facades\Auth;

class SuhuHarianController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            // Admin melihat SEMUA suhu harian dari semua kebun
            $suhu = SuhuHarian::with('kebun.user')->orderBy('tanggal', 'asc')->get();
        } else {
            // Petani hanya melihat suhu dari kebunnya sendiri
            $suhu = SuhuHarian::whereHas('kebun', function ($q) {
                $q->where('user_id', Auth::id());
            })->with('kebun')->orderBy('tanggal', 'asc')->get();
        }
        
        $suhuGrouped = $suhu->groupBy('kebun_id');

        // Total GDD per kebun (respects tanggal_berbunga)
        if (Auth::user()->role === 'admin') {
            $kebuns = Kebun::with('fase')->get();
        } else {
            $kebuns = Kebun::with('fase')->where('user_id', Auth::id())->get();
        }

        $statsGdd = [];
        foreach ($kebuns as $k) {
            $statsGdd[] = [
                'id' => $k->id,
                'nama' => $k->nama_kebun,
                'total' => $k->total_gdd,
                'target' => $k->target_gdd,
                'progress' => $k->gdd_progress,
                'fase' => $k->fase_otomatis
            ];
        }

        return view('suhu.index', compact('suhuGrouped', 'statsGdd'));
    }

    public function create()
    {
        if (Auth::user()->role === 'admin') {
            return redirect('/admin/dashboard')->with('error', 'Admin tidak dapat menginput suhu. Silakan login sebagai Petani.');
        }

        // Petani hanya bisa pilih kebunnya sendiri
        $kebun = Kebun::where('user_id', Auth::id())->get();
        return view('suhu.create', compact('kebun'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role === 'admin') {
            return redirect('/admin/dashboard');
        }

        $request->validate([
            'kebun_id' => 'required|exists:kebuns,id',
            'tanggal'  => 'required|date',
            'tmax'     => 'required|numeric',
            'tmin'     => 'required|numeric|lte:tmax'
        ], [
            'tmin.lte' => 'Tmin tidak boleh lebih besar dari Tmax.'
        ]);

        // Pastikan kebun milik petani ini
        Kebun::where('id', $request->kebun_id)
             ->where('user_id', Auth::id())
             ->firstOrFail();

        // Prevent duplicate: same date + same kebun
        $exists = SuhuHarian::where('kebun_id', $request->kebun_id)
            ->where('tanggal', $request->tanggal)
            ->exists();

        if ($exists) {
            return redirect('/suhu')->with('error', 'Data suhu untuk kebun ini pada tanggal tersebut sudah ada.');
        }

        $gdd = max(0, (($request->tmax + $request->tmin) / 2) - 10);

        SuhuHarian::create([
            'kebun_id' => $request->kebun_id,
            'tanggal'  => $request->tanggal,
            'tmin'     => $request->tmin,
            'tmax'     => $request->tmax,
            'gdd'      => $gdd
        ]);

        return redirect('/suhu')->with('success', 'Data suhu & GDD berhasil disimpan.');
    }

    public function destroy($id)
    {
        $suhu = SuhuHarian::with('kebun')->findOrFail($id);

        if (Auth::user()->role !== 'admin' && $suhu->kebun->user_id !== Auth::id()) {
            abort(403);
        }

        $suhu->delete();
        $redirect = Auth::user()->role === 'admin' ? '/admin/dashboard' : '/suhu';
        return redirect($redirect)->with('success', 'Data suhu berhasil dihapus.');
    }
}
