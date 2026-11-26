@extends('layouts.app')

@section('content')
<div class="card">
    <h1>Edit Penawaran</h1>

    <p>
        <b>Proyek:</b> {{ $project->judul }}
    </p>

    <p>
        <b>Developer:</b> {{ $bid->developer->nama }}
    </p>

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST"
          action="{{ route('bids.update', [$project, $bid]) }}">
        @csrf
        @method('PUT')

        <label>Harga Penawaran (Rp)</label>
        <input type="number"
               name="harga"
               min="1"
               value="{{ old('harga', $bid->harga) }}"
               required>

        <label>Durasi (hari)</label>
        <input type="number"
               name="durasi"
               min="1"
               value="{{ old('durasi', $bid->durasi) }}"
               required>

        <label>Proposal</label>
        <textarea name="proposal"
                  maxlength="5000"
                  required>{{ old('proposal', $bid->proposal) }}</textarea>

        <button class="btn" type="submit">
            Simpan Perubahan
        </button>

        <a href="{{ route('projects.show', $project) }}">
            Batal
        </a>
    </form>
</div>
@endsection
