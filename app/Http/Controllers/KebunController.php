<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kebun;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class KebunController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            // Admin melihat SEMUA kebun beserta pemiliknya
            $kebun = Kebun::with(['fase', 'suhu'])->get();
            return view('kebun.index', compact('kebun'));
        }

        // Petani hanya melihat kebunnya sendiri
        $kebun = Kebun::where('user_id', Auth::id())->get();
        return view('kebun.index', compact('kebun'));
    }

    public function create()
    {
        // Hanya petani yang boleh menambah kebun baru
        if (Auth::user()->role === 'admin') {
            return redirect('/admin/dashboard')->with('error', 'Admin tidak dapat menambah kebun. Silakan login sebagai Petani.');
        }
        return view('kebun.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role === 'admin') {
            return redirect('/admin/dashboard')->with('error', 'Admin tidak dapat menambah kebun.');
        }

        $request->validate([
            'nama_kebun'  => 'required',
            'lokasi'      => 'required',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
            'jumlah_pohon'=> 'required|integer|min:1',
            'jenis_mangga'=> 'required',
        ]);

        Kebun::create([
            'user_id'     => Auth::id(),
            'nama_kebun'  => $request->nama_kebun,
            'lokasi'      => $request->lokasi,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'jumlah_pohon'=> $request->jumlah_pohon,
            'jenis_mangga'=> $request->jenis_mangga,
        ]);

        return redirect('/kebun')->with('success', 'Kebun berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kebun = Kebun::findOrFail($id);

        // Petani hanya boleh edit kebunnya sendiri
        if (Auth::user()->role !== 'admin' && $kebun->user_id !== Auth::id()) {
            abort(403, 'Anda tidak diizinkan mengakses kebun ini.');
        }

        return view('kebun.edit', compact('kebun'));
    }

    public function update(Request $request, $id)
    {
        $kebun = Kebun::findOrFail($id);

        if (Auth::user()->role !== 'admin' && $kebun->user_id !== Auth::id()) {
            abort(403);
        }

        $isSiklusAktif = $kebun->fase && $kebun->fase->tanggal_berbunga;

        $request->validate([
            'nama_kebun'  => 'required',
            'lokasi'      => 'required',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
            'jumlah_pohon'=> 'required|integer|min:1',
            // If siklus is active, ignore jenis_mangga validation since we will drop it anyway
            'jenis_mangga'=> $isSiklusAktif ? 'nullable' : 'required',
        ]);

        $updateData = [
            'nama_kebun'  => $request->nama_kebun,
            'lokasi'      => $request->lokasi,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'jumlah_pohon'=> $request->jumlah_pohon,
        ];

        // Prevent modification of jenis_mangga if cycle is running
        if (!$isSiklusAktif && $request->filled('jenis_mangga')) {
            $updateData['jenis_mangga'] = $request->jenis_mangga;
        } elseif ($isSiklusAktif && $request->filled('jenis_mangga') && $request->jenis_mangga !== $kebun->jenis_mangga) {
            return redirect()->back()->with('error', 'Varietas tidak bisa diubah saat siklus pertumbuhan sedang aktif.');
        }

        $kebun->update($updateData);

        $redirect = Auth::user()->role === 'admin' ? '/admin/dashboard' : '/kebun';
        return redirect($redirect)->with('success', 'Data kebun berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kebun = Kebun::findOrFail($id);

        // Hanya admin yang boleh hapus sembarang kebun
        if (Auth::user()->role !== 'admin' && $kebun->user_id !== Auth::id()) {
            abort(403);
        }

        $kebun->delete();

        $redirect = Auth::user()->role === 'admin' ? '/admin/dashboard' : '/kebun';
        return redirect($redirect)->with('success', 'Kebun berhasil dihapus.');
    }
}