
@extends('layouts.app')

@section('content')
    <div class="card">
        <h1>{{ $project->judul }}</h1>

        <p><b>Klien:</b> {{ $project->client->nama }}</p>
        <p><b>Kategori:</b> {{ $project->kategori }}</p>

        <p><b>Deskripsi:</b></p>
        <p style="white-space: pre-wrap">{{ $project->deskripsi }}</p>

        <p>
            <b>Budget:</b>
            Rp {{ number_format($project->budget_min, 0, ',', '.') }}
            -
            Rp {{ number_format($project->budget_max, 0, ',', '.') }}
        </p>

        <p>
            <b>Batas Lelang:</b>
            {{ $project->batas_lelang->format('d-m-Y') }}
        </p>

        <p><b>Status:</b> {{ ucfirst($project->status) }}</p>

        <a class="btn" href="{{ route('projects.edit', $project) }}">
            Edit Proyek
        </a>

        <form action="{{ route('projects.destroy', $project) }}"
              method="POST" style="display: inline">
            @csrf
            @method('DELETE')

            <button class="btn" type="submit">
                Hapus Proyek
            </button>
        </form>
    </div>

    <h2>Daftar Penawaran</h2>

    @forelse ($project->bids as $bid)
        <div class="card">
            <p><b>Developer:</b> {{ $bid->developer->nama }}</p>
            <p>
                <b>Harga:</b>
                Rp {{ number_format($bid->harga, 0, ',', '.') }}
            </p>
            <p><b>Durasi:</b> {{ $bid->durasi }} hari</p>
            <p>{{ $bid->proposal }}</p>

            @if ($bid->is_winner)
                <p class="success">Pemenang lelang</p>
            @endif
        </div>
    @empty
        <p>Belum ada penawaran.</p>
    @endforelse

    <a href="{{ route('projects.index') }}">
        Kembali ke Daftar Proyek
    </a>
@endsection
