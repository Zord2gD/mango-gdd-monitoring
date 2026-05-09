<?php

namespace App\Http\Controllers;

use App\Models\FaseTanaman;
use App\Models\Kebun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaseTanamanController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            // Admin melihat SEMUA fase dari semua kebun
            $fase = FaseTanaman::with('kebun.user')->get();
        } else {
            // Petani hanya melihat fase dari kebunnya sendiri
            $fase = FaseTanaman::whereHas('kebun', function ($q) {
                $q->where('user_id', Auth::id());
            })->with('kebun')->get();
        }

        return view('fase.index', compact('fase'));
    }

    public function create()
    {
        if (Auth::user()->role === 'admin') {
            return redirect('/admin/dashboard')->with('error', 'Admin tidak dapat menambah Fase. Silakan login sebagai Petani.');
        }

        // Petani hanya bisa pilih kebunnya sendiri
        $kebun = Kebun::where('user_id', Auth::id())->get();
        return view('fase.create', compact('kebun'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role === 'admin') {
            return redirect('/admin/dashboard');
        }

        $request->validate([
            'kebun_id'         => 'required|exists:kebuns,id',
            'tanggal_berbunga' => 'required|date',
        ]);

        // Pastikan kebun milik petani ini
        $kebun = Kebun::where('id', $request->kebun_id)
                      ->where('user_id', Auth::id())
                      ->firstOrFail();

        FaseTanaman::updateOrCreate(
            ['kebun_id' => $kebun->id],
            ['tanggal_berbunga' => $request->tanggal_berbunga]
        );

        return redirect('/fase')->with('success', 'Fase tanaman berhasil disimpan.');
    }

    public function edit($id)
    {
        $fase = FaseTanaman::with('kebun')->findOrFail($id);

        if (Auth::user()->role !== 'admin' && $fase->kebun->user_id !== Auth::id()) {
            abort(403);
        }

        $kebun = Auth::user()->role === 'admin'
            ? Kebun::all()
            : Kebun::where('user_id', Auth::id())->get();

        return view('fase.edit', compact('fase', 'kebun'));
    }

    public function update(Request $request, $id)
    {
        $fase = FaseTanaman::with('kebun')->findOrFail($id);

        if (Auth::user()->role !== 'admin' && $fase->kebun->user_id !== Auth::id()) {
            abort(403);
        }

        $fase->update([
            'tanggal_berbunga' => $request->tanggal_berbunga,
        ]);

        $redirect = Auth::user()->role === 'admin' ? '/admin/dashboard' : '/fase';
        return redirect($redirect)->with('success', 'Fase berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $fase = FaseTanaman::with('kebun')->findOrFail($id);

        if (Auth::user()->role !== 'admin' && $fase->kebun->user_id !== Auth::id()) {
            abort(403);
        }

        $fase->delete();
        $redirect = Auth::user()->role === 'admin' ? '/admin/dashboard' : '/fase';
        return redirect($redirect)->with('success', 'Fase berhasil dihapus.');
    }
}
