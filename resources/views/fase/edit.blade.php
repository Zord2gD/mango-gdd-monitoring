@extends('layouts.dashboard')

@section('content')

<h2>Edit Fase Tanaman</h2>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('fase.update', $fase->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Kebun</label>
        <select name="kebun_id" class="form-control">
            @foreach($kebun as $k)
                <option value="{{ $k->id }}" {{ $fase->kebun_id == $k->id ? 'selected' : '' }}>
                    {{ $k->nama_kebun }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Tanggal Berbunga</label>
        <input type="date" name="tanggal_berbunga" value="{{ $fase->tanggal_berbunga }}" class="form-control">
    </div>


    <button class="btn btn-success">Simpan Perubahan</button>
    <a href="{{ route('fase.index') }}" class="btn btn-secondary ms-2">Batal</a>
</form>

@endsection
