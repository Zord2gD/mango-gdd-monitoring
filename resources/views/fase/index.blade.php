@extends('layouts.dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Data Fase Tanaman</h2>
    @if(Auth::user()->role !== 'admin')
        <a href="{{ route('fase.create') }}" class="btn btn-success">+ Tambah Fase</a>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered table-hover">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            @if(Auth::user()->role === 'admin')
                <th>Petani</th>
            @endif
            <th>Nama Kebun</th>
            <th>Tanggal Berbunga</th>
            <th>Fase Sekarang</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($fase as $i => $f)
            <tr>
                <td>{{ $i + 1 }}</td>
                @if(Auth::user()->role === 'admin')
                    <td>{{ $f->kebun->user->name ?? '—' }}</td>
                @endif
                <td>{{ $f->kebun->nama_kebun }}</td>
                <td>{{ $f->tanggal_berbunga }}</td>
                <td>
                    @php
                        $otomatis = $f->kebun->fase_otomatis;
                        $label = match($otomatis) {
                            'Berbunga'                  => ['class' => 'bg-warning text-dark'],
                            'Pembentukan Buah (Pentil)' => ['class' => 'bg-info text-dark'],
                            'Pembesaran Buah'           => ['class' => 'bg-primary'],
                            'Pematangan'                => ['class' => 'bg-success'],
                            'Siap Panen'                => ['class' => 'bg-success', 'style' => 'background-color: #15803d !important;'],
                            default                     => ['class' => 'bg-secondary'],
                        };
                        $style = $label['style'] ?? '';
                    @endphp
                    <span class="badge {{ $label['class'] }}" style="{{ $style }}">{{ $otomatis }}</span>
                </td>
                <td>
                    <a href="{{ route('fase.edit', $f->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('fase.destroy', $f->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data fase ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    @if(Auth::user()->role === 'admin')
                        Belum ada data fase tanaman yang terdaftar.
                    @else
                        Anda belum menginput fase tanaman. <a href="{{ route('fase.create') }}">Input sekarang</a>.
                    @endif
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection