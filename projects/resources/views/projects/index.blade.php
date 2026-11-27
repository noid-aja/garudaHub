@extends('layouts.app')

@section('content')
    <h1>Daftar Proyek Garuda-Hub</h1>

    @auth
        @if (auth()->user()->role === 'klien')
            <a class="btn" href="{{ route('projects.create') }}">
                + Tambah Proyek
            </a>
        @endif
    @endauth

    <form method="GET" action="{{ route('projects.index') }}"
          class="search-form">
        <input type="text" name="cari"
               placeholder="Cari judul atau kategori..."
               value="{{ request('cari') }}">
        <button class="btn" type="submit">Cari</button>
    </form>

    @forelse ($projects as $project)
        <div class="card">
            <h2>
                <a href="{{ route('projects.show', $project) }}">
                    {{ $project->judul }}
                </a>
            </h2>

            <p>Kategori: {{ $project->kategori }}</p>
            <p>Klien: {{ $project->client->nama }}</p>
            <p>
                Budget: Rp {{ number_format($project->budget_min, 0, ',', '.') }}
                -
                Rp {{ number_format($project->budget_max, 0, ',', '.') }}
            </p>
            <p>Status: {{ ucfirst($project->status) }}</p>
            <p>Jumlah penawaran: {{ $project->bids_count }}</p>

            <a class="btn"
               href="{{ route('projects.show', $project) }}">
                Lihat Detail
            </a>
        </div>
    @empty
        <p>Belum ada proyek.</p>
    @endforelse

    {{ $projects->withQueryString()->links('pagination.simple') }}
@endsection
