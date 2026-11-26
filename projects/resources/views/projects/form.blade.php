
@extends('layouts.app')

@section('content')
    <div class="card">
        <h1>
            {{ $project ? 'Edit Proyek' : 'Tambah Proyek' }}
        </h1>

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST"
              action="{{ $project
                  ? route('projects.update', $project)
                  : route('projects.store') }}">

            @csrf

            @if ($project)
                @method('PUT')
            @endif

            <label>Klien</label>
            <select name="client_id" required>
                <option value="">Pilih Klien</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}"
                        @selected(old('client_id', $project?->client_id) == $client->id)>
                        {{ $client->nama }}
                    </option>
                @endforeach
            </select>

            <label>Judul Proyek</label>
            <input type="text" name="judul" maxlength="100"
                   value="{{ old('judul', $project?->judul) }}"
                   required>

            <label>Deskripsi</label>
            <textarea name="deskripsi" required>{{ old('deskripsi', $project?->deskripsi) }}</textarea>

            <label>Kategori</label>
            <input type="text" name="kategori"
                   value="{{ old('kategori', $project?->kategori) }}"
                   required>

            <label>Budget Minimum</label>
            <input type="number" name="budget_min" min="0"
                   value="{{ old('budget_min', $project?->budget_min ?? 0) }}"
                   required>

            <label>Budget Maksimum</label>
            <input type="number" name="budget_max" min="0"
                   value="{{ old('budget_max', $project?->budget_max ?? 0) }}"
                   required>

            <label>Batas Lelang</label>
            <input type="date" name="batas_lelang"
                   value="{{ old('batas_lelang', $project?->batas_lelang?->format('Y-m-d')) }}"
                   required>

            <label>Status</label>
            <select name="status">
                @foreach (['terbuka', 'ditutup'] as $status)
                    <option value="{{ $status }}"
                        @selected(old('status', $project?->status ?? 'terbuka') === $status)>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>

            <button class="btn" type="submit">
                Simpan Proyek
            </button>

            <a href="{{ route('projects.index') }}">
                Batal
            </a>
        </form>
    </div>
@endsection
