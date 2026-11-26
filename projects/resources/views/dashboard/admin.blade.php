
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
    Kelola Proyek
</a>

<a href="{{ route('dashboard.index') }}">
    Kembali ke Dashboard
</a>
@endsection
