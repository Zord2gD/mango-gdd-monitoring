@extends('layouts.dashboard')

@section('content')

    <h2>Tambah Fase Tanaman</h2>

    <form action="{{ route('fase.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Pilih Kebun</label>
            <select name="kebun_id" class="form-control">
                @foreach($kebun as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kebun }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Tanggal Berbunga</label>
            <input type="date" name="tanggal_berbunga" class="form-control">
        </div>

        <button class="btn btn-success">Simpan</button>

    </form>

@endsection