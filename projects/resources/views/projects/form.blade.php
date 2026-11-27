@extends('layouts.app')

@section('content')
<div class="card">
    <h1>{{ $project ? 'Edit Proyek' : 'Tambah Proyek' }}</h1>

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ $project ? route('projects.update', $project) : route('projects.store') }}">
        @csrf
        @if ($project)
            @method('PUT')
        @endif

        <p><b>Klien:</b> {{ auth()->user()->nama }}</p>

        <label>Judul Proyek</label>
        <input type="text" name="judul" maxlength="100"
               value="{{ old('judul', $project?->judul) }}" required>

        <label>Deskripsi</label>
        <textarea name="deskripsi" required>{{ old('deskripsi', $project?->deskripsi) }}</textarea>

        <label>Kategori</label>
        <input type="text" name="kategori"
               value="{{ old('kategori', $project?->kategori) }}" required>

        <label>Budget Minimum (Rp)</label>
        <input type="text" name="budget_min" id="budget_min" inputmode="numeric"
               value="{{ number_format((int) preg_replace('/\D/', '', (string) old('budget_min', $project?->budget_min ?? 0)), 0, ',', '.') }}" required>

        <label>Budget Maksimum (Rp)</label>
        <input type="text" name="budget_max" id="budget_max" inputmode="numeric"
               value="{{ number_format((int) preg_replace('/\D/', '', (string) old('budget_max', $project?->budget_max ?? 0)), 0, ',', '.') }}" required>

        <label>Batas Lelang</label>
        <input type="date" name="batas_lelang"
               value="{{ old('batas_lelang', $project?->batas_lelang?->format('Y-m-d')) }}" required>

        <p><b>Status:</b> {{ ucfirst($project?->status ?? 'terbuka') }}</p>

        <button class="btn" type="submit">Simpan Proyek</button>
        <a href="{{ route('projects.index') }}">Batal</a>
    </form>
</div>

@endsection
