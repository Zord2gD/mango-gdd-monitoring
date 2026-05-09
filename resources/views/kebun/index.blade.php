@extends('layouts.dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Data Kebun</h2>
    @if(Auth::user()->role !== 'admin')
        <a href="{{ route('kebun.create') }}" class="btn btn-success">+ Tambah Kebun</a>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<table class="table table-bordered table-hover">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            @if(Auth::user()->role === 'admin')
                <th>Petani</th>
            @endif
            <th>Nama Kebun</th>
            <th>Lokasi</th>
            <th>Jumlah Pohon</th>
            <th>Jenis Mangga</th>
            @if(Auth::user()->role === 'admin')
                <th>Total GDD</th>
                <th>Status Panen</th>
            @endif
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($kebun as $i => $k)
            <tr>
                <td>{{ $i + 1 }}</td>
                @if(Auth::user()->role === 'admin')
                    <td>{{ $k->user->name ?? '—' }}</td>
                @endif
                <td>{{ $k->nama_kebun }}</td>
                <td>{{ $k->lokasi }}</td>
                <td>{{ $k->jumlah_pohon }}</td>
                <td>{{ $k->jenis_mangga }}</td>
                @if(Auth::user()->role === 'admin')
                    <td><strong>{{ number_format($k->total_gdd, 1) }}</strong> / {{ $k->target_gdd }}</td>
                    <td>
                        @if($k->is_siap_panen)
                            <span class="badge bg-success">Siap Panen</span>
                        @elseif($k->gdd_progress >= 75)
                            <span class="badge bg-warning text-dark">Hampir Panen</span>
                        @else
                            <span class="badge bg-secondary">Belum</span>
                        @endif
                    </td>
                @endif
                <td>
                    <a href="{{ route('kebun.edit', $k->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('kebun.destroy', $k->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data?')">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="text-center text-muted py-4">
                    @if(Auth::user()->role === 'admin')
                        Belum ada data kebun yang terdaftar.
                    @else
                        Anda belum memiliki kebun. <a href="{{ route('kebun.create') }}">Tambah sekarang</a>.
                    @endif
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection