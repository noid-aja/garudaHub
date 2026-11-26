
@extends('layouts.app')

@section('content')
<h1>Dashboard Klien</h1>

<div class="card">
    <h2>Selamat datang, {{ $user->nama }}</h2>
    <p>Total proyek: {{ $projects->count() }}</p>

    <a class="btn" href="{{ route('projects.create') }}">
        Tambah Proyek
    </a>
</div>

<h2>Proyek Saya</h2>

@forelse ($projects as $project)
    <div class="card">
        <h3>{{ $project->judul }}</h3>

        <p>Status: {{ ucfirst($project->status) }}</p>

        <p>Jumlah Penawaran: {{ $project->bids_count }}</p>

        <a class="btn"
           href="{{ route('projects.show', $project) }}">
            Lihat Proyek
        </a>
    </div>
@empty
    <div class="card">
        <p>Belum memiliki proyek.</p>
    </div>
@endforelse

<a href="{{ route('dashboard.index') }}">
    Kembali ke Dashboard
</a>
@endsection
