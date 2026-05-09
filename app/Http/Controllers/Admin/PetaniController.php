<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class PetaniController extends Controller
{
    public function index()
    {
        // Get all farmers with their farms setup
        $petani = User::where('role', 'petani')
            ->with(['kebun' => fn($q) => $q->with('fase', 'suhu')])
            ->get();

        // Calculate summary metrics
        $totalPetani = $petani->count();
        $petaniAktif = $petani->filter(fn($p) => $p->kebun->count() > 0)->count();
        
        $petaniSiapPanen = $petani->filter(function($p) {
            return $p->kebun->contains(fn($k) => $k->is_siap_panen);
        })->count();

        // Process data for the view
        $dataPetani = $petani->map(function($p) {
            $jumlahKebun = $p->kebun->count();
            
            // Calculate total GDD across all farms of this farmer
            $totalGdd = $p->kebun->sum('total_gdd');
            $targetGdd = $p->kebun->sum('target_gdd');
            
            // Average progress overall
            $progressOverall = $targetGdd > 0 ? min(100, round(($totalGdd / $targetGdd) * 100)) : 0;
            
            // Status overall 
            $siapPanenCount = $p->kebun->filter(fn($k) => $k->is_siap_panen)->count();
            $hampirPanenCount = $p->kebun->filter(fn($k) => $k->gdd_progress >= 75 && !$k->is_siap_panen)->count();
            
            if ($siapPanenCount > 0) {
                $statusPanen = 'Siap Panen';
                $statusBadge = 'badge-green';
            } elseif ($hampirPanenCount > 0) {
                $statusPanen = 'Hampir Panen';
                $statusBadge = 'badge-yellow';
            } else {
                $statusPanen = 'Belum Panen';
                $statusBadge = 'badge-gray';
            }
            
            return (object) [
                'id' => $p->id,
                'nama' => $p->name,
                'email' => $p->email,
                'jumlah_kebun' => $jumlahKebun,
                'total_gdd' => number_format($totalGdd, 1),
                'progress' => $progressOverall,
                'status_panen' => $statusPanen,
                'status_badge' => $statusBadge,
                'created_at' => $p->created_at->format('d M Y')
            ];
        });

        return view('admin.petani.index', compact('totalPetani', 'petaniAktif', 'petaniSiapPanen', 'dataPetani'));
    }
}
