@extends('layouts.app')

@section('content')
<h1>Kelola Proyek</h1>

<div class="card">
    <a href="{{ route('dashboard.admin') }}">Kembali ke Dashboard Admin</a>
</div>

@forelse ($projects as $project)
    <div class="card">
        <h3>
            <a href="{{ route('projects.show', $project) }}">{{ $project->judul }}</a>
        </h3>
        <p>Klien: {{ $project->client->nama }}</p>
        <p>Status: {{ ucfirst($project->status) }}</p>
        <p>Jumlah penawaran: {{ $project->bids_count }}</p>

        <form method="POST" action="{{ route('admin.projects.destroy', $project) }}"
              data-confirm="Hapus proyek dan semua penawarannya?">
            @csrf
            @method('DELETE')
            <button class="btn" type="submit">Hapus Proyek</button>
        </form>
    </div>
@empty
    <p>Belum ada proyek.</p>
@endforelse

{{ $projects->links('pagination.simple') }}
@endsection
