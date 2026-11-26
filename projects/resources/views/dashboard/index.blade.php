
@extends('layouts.app')

@section('content')
<h1>Dashboard Garuda-Hub</h1>

<div class="card">
    <p>Mode simulasi lokal: pilih akun untuk melihat dashboard.</p>
</div>

<div class="card">
    <h2>Dashboard Klien</h2>

    @foreach ($clients as $client)
        <p>
            <a href="{{ route('dashboard.klien', $client) }}">
                {{ $client->nama }}
            </a>
        </p>
    @endforeach
</div>

<div class="card">
    <h2>Dashboard Developer</h2>

    @foreach ($developers as $developer)
        <p>
            <a href="{{ route('dashboard.developer', $developer) }}">
                {{ $developer->nama }}
            </a>
        </p>
    @endforeach
</div>

<div class="card">
    <h2>Dashboard Admin</h2>

    <a class="btn" href="{{ route('dashboard.admin') }}">
        Lihat Dashboard Admin
    </a>
</div>
@endsection
