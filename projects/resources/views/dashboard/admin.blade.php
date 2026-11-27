@extends('layouts.app')

@section('content')
<h1>Dashboard Admin</h1>

<div class="card">
    <h2>Ringkasan Garuda-Hub</h2>

    <p>Total Pengguna: {{ $statistik['users'] }}</p>

    <p>Total Proyek: {{ $statistik['projects'] }}</p>

    <p>Total Penawaran: {{ $statistik['bids'] }}</p>

    <p>Proyek Terbuka: {{ $statistik['terbuka'] }}</p>

    <p>Proyek Ditutup: {{ $statistik['ditutup'] }}</p>
</div>

<a class="btn" href="{{ route('projects.index') }}">
    Lihat Daftar Proyek
</a>

<p><a class="btn" href="{{ route('admin.users') }}">Kelola Pengguna</a></p>

<p><a class="btn" href="{{ route('admin.projects') }}">Kelola Proyek</a></p>

<a href="{{ route('dashboard.index') }}">
    Kembali ke Dashboard
</a>
@endsection
