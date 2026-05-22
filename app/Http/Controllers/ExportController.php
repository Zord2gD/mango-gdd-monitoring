<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExportController extends Controller
{
    // --- ADMIN EXPORTS ---
    public function adminCsv()
    {
        $kebuns = \App\Models\Kebun::with(['user', 'fase', 'suhu'])->get();
        $filename = "admin_rekapitulasi_kebun_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($kebuns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Nama Kebun', 'Pemilik (Petani)', 'Lokasi', 'Varietas', 'Progress GDD', 'Status Panen']);

            foreach ($kebuns as $i => $k) {
                $status = 'Sedang Berkembang';
                if ($k->gdd_progress < 30) $status = 'Awal Pertumbuhan';
                elseif ($k->gdd_progress < 100) $status = 'Hampir Panen';
                if ($k->is_siap_panen) $status = 'Siap Panen';

                fputcsv($file, [
                    $i + 1,
                    $k->nama_kebun,
                    $k->user->name ?? '-',
                    $k->lokasi,
                    $k->jenis_mangga,
                    round($k->total_gdd, 1) . ' / ' . $k->target_gdd,
                    $status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function adminPdf()
    {
        $title = "Laporan Rekapitulasi Global Kebun & Petani";
        $kebuns = \App\Models\Kebun::with(['user', 'fase', 'suhu'])->get();
        return view('export.print', compact('kebuns', 'title'))->with('role', 'admin');
    }

    // --- PETANI EXPORTS ---
    public function petaniCsv()
    {
        $user_id = \Illuminate\Support\Facades\Auth::id();
        $riwayats = \App\Models\RiwayatPanen::whereHas('kebun', function($q) use ($user_id) {
            $q->where('user_id', $user_id);
        })->with('kebun')->orderBy('tanggal_panen', 'desc')->get();
        
        $filename = "petani_riwayat_panen_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($riwayats) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Nama Kebun', 'Tanggal Berbunga', 'Tanggal Panen', 'Lama Siklus (Hari)', 'Total GDD', 'Hasil Panen (Kg)', 'Catatan']);

            foreach ($riwayats as $i => $r) {
                $lama = $r->tanggal_berbunga ? \Carbon\Carbon::parse($r->tanggal_berbunga)->diffInDays(\Carbon\Carbon::parse($r->tanggal_panen)) : 0;
                fputcsv($file, [
                    $i + 1,
                    $r->kebun->nama_kebun ?? '-',
                    $r->tanggal_berbunga ?? '-',
                    $r->tanggal_panen,
                    $r->tanggal_berbunga ? $lama . ' Hari' : '-',
                    round($r->total_gdd, 1),
                    round($r->hasil_panen_kg, 1),
                    $r->catatan ?? '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function petaniPdf()
    {
        $title = "Laporan Riwayat Panen Saya";
        $user_id = \Illuminate\Support\Facades\Auth::id();
        $riwayats = \App\Models\RiwayatPanen::whereHas('kebun', function($q) use ($user_id) {
            $q->where('user_id', $user_id);
        })->with('kebun')->orderBy('tanggal_panen', 'desc')->get();
        
        return view('export.print', compact('riwayats', 'title'))->with('role', 'petani');
    }

    // --- PENGEPUL EXPORTS ---
    public function pengepulCsv()
    {
        // Hanya export yang siap panen
        $kebuns = \App\Models\Kebun::with(['user', 'fase', 'suhu'])->get()->filter(function($k) {
            return $k->is_siap_panen;
        });

        $filename = "pengepul_rute_siap_panen_" . date('Ymd_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($kebuns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Nama Kebun', 'Nama Petani', 'Lokasi/Alamat', 'Varietas Mangga', 'Total Pohon', 'Status']);

            $i = 0;
            foreach ($kebuns as $k) {
                fputcsv($file, [
                    $i + 1,
                    $k->nama_kebun,
                    $k->user->name ?? '-',
                    $k->lokasi,
                    $k->jenis_mangga,
                    $k->jumlah_pohon,
                    'SIAP PANEN (GDD: ' . round($k->total_gdd, 1) . ')'
                ]);
                $i++;
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function pengepulPdf()
    {
        $title = "Laporan Rute Logistik Kebun Siap Panen";
        $kebuns = \App\Models\Kebun::with(['user', 'fase', 'suhu'])->get()->filter(function($k) {
            return $k->is_siap_panen;
        });
        return view('export.print', compact('kebuns', 'title'))->with('role', 'pengepul');
    }
}
