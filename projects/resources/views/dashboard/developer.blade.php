@extends('layouts.app')

@section('content')
<h1>Dashboard Developer</h1>

<div class="card">
    <h2>Selamat datang, {{ $user->nama }}</h2>

    <p>Total Penawaran: {{ $bids->total() }}</p>
</div>

<h2>Riwayat Penawaran Saya</h2>

@forelse ($bids as $bid)
    <div class="card">
        <h3>{{ $bid->project->judul }}</h3>

        <p>
            Harga:
            Rp {{ number_format($bid->harga, 0, ',', '.') }}
        </p>

        <p>Durasi: {{ $bid->durasi }} hari</p>

        <p>
            Status:
            {{ $bid->is_winner ? 'Menang' : 'Belum menang' }}
        </p>

        <a class="btn"
           href="{{ route('projects.show', $bid->project) }}">
            Lihat Proyek
        </a>
    </div>
@empty
    <div class="card">
        <p>Belum mengajukan penawaran.</p>
    </div>
@endforelse

{{ $bids->links() }}

<a href="{{ route('dashboard.index') }}">
    Kembali ke Dashboard
</a>
@endsection
