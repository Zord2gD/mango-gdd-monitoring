<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kebun;
use App\Models\User;
use App\Models\SuhuHarian;

class DashboardController extends Controller
{
    /**
     * Redirect user to their respective dashboard based on role.
     */
    public function redirect()
    {
        $role = Auth::user()->role;

        if ($role == 'admin') {
            return redirect('/admin/dashboard');
        } elseif ($role == 'petani') {
            return redirect('/petani/dashboard');
        } else {
            return redirect('/pengepul/dashboard');
        }
    }

    /**
     * Show the Admin Dashboard.
     */
    public function admin()
    {
        $totalKebun    = Kebun::count();
        $totalPetani   = User::where('role', 'petani')->count();
        $allKebun      = Kebun::with(['user', 'fase', 'suhu'])->get();
        $siapPanen     = $allKebun->filter(fn($k) => $k->is_siap_panen)->count();
        $avgGdd        = $allKebun->avg('total_gdd');

        return view('dashboard.admin', compact('totalKebun', 'totalPetani', 'siapPanen', 'avgGdd', 'allKebun'));
    }

    /**
     * Show the Petani Dashboard.
     */
    public function petani()
    {
        $kebun = Kebun::where('user_id', Auth::id())
            ->with(['fase', 'suhu'])
            ->get();

        // Calculate summary using collections
        $totalKebun = $kebun->count();
        $totalPohon = $kebun->sum('jumlah_pohon');
        $totalGdd = $kebun->sum('total_gdd');
        
        $hampirPanen = $kebun->filter(function($k) {
            $progress = min(100, $k->gdd_progress);
            return $progress >= 70 && $progress < 90;
        })->count();

        $siapPanen = $kebun->filter(function($k) {
            $progress = min(100, $k->gdd_progress);
            return $progress >= 90;
        })->count();

        // Find the kebun closest to harvest for the "GDD Status" prominent card
        $topKebun = $kebun->sortByDesc('gdd_progress')->first();

        return view('dashboard.petani', compact('kebun', 'totalKebun', 'totalPohon', 'totalGdd', 'hampirPanen', 'siapPanen', 'topKebun'));
    }

    /**
     * Show the Pengepul Dashboard.
     */
    public function pengepul()
    {
        // Eager load user, fase, & suhu for Hybrid Caching
        $allKebun = Kebun::with(['user', 'fase', 'suhu'])
            ->latest()
            ->get();

        // Append GDD accessors to each kebun
        $allKebun->each->append(['total_gdd', 'gdd_progress', 'is_siap_panen']);

        $totalKebun  = $allKebun->count();
        $totalPetani = User::where('role', 'petani')->count();
        $siapPanen   = $allKebun->filter(fn($k) => $k->is_siap_panen)->count();
        $hampirPanen = $allKebun->filter(function ($k) {
            $progress = min(100, $k->gdd_progress);
            return $progress >= 70 && $progress < 100;
        })->count();

        return view('dashboard.pengepul', compact(
            'allKebun', 'totalKebun', 'totalPetani', 'siapPanen', 'hampirPanen'
        ));
    }
}
