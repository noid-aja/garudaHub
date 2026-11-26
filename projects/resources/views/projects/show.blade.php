@extends('layouts.app')

@section('content')
<div class="card">
    <h1>{{ $project->judul }}</h1>

    <p><b>Klien:</b> {{ $project->client->nama }}</p>
    <p><b>Kategori:</b> {{ $project->kategori }}</p>
    <p style="white-space: pre-wrap">{{ $project->deskripsi }}</p>

    <p>
        <b>Budget:</b>
        Rp {{ number_format($project->budget_min, 0, ',', '.') }}
        -
        Rp {{ number_format($project->budget_max, 0, ',', '.') }}
    </p>

    <p>
        <b>Batas lelang:</b>
        {{ $project->batas_lelang->format('d-m-Y') }}
    </p>

    <p><b>Status:</b> {{ ucfirst($project->status) }}</p>

    @auth
        @if (
            auth()->user()->role === 'klien'
            && auth()->id() === $project->client_id
            && !$project->bids->contains('is_winner', true)
        )
            <a class="btn"
               href="{{ route('projects.edit', $project) }}">
                Edit Proyek
            </a>

            <form method="POST"
                  action="{{ route('projects.destroy', $project) }}"
                  style="display:inline">
                @csrf
                @method('DELETE')

                <button class="btn" type="submit">
                    Hapus Proyek
                </button>
            </form>
        @endif
    @endauth
</div>

@if ($errors->any())
    <div class="card error">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

@auth
    @if (
        auth()->user()->role === 'developer'
        && $project->status === 'terbuka'
        && !$project->batas_lelang->isBefore(today())
    )
        <div class="card">
            <h2>Ajukan Penawaran</h2>

            <form method="POST"
                  action="{{ route('bids.store', $project) }}">
                @csrf

                <p>
                    Developer:
                    <b>{{ auth()->user()->nama }}</b>
                </p>

                <label>Harga (Rp)</label>
                <input type="number" name="harga" min="1"
                       value="{{ old('harga') }}" required>

                <label>Durasi (hari)</label>
                <input type="number" name="durasi" min="1"
                       value="{{ old('durasi') }}" required>

                <label>Proposal</label>
                <textarea name="proposal"
                          maxlength="5000"
                          required>{{ old('proposal') }}</textarea>

                <button class="btn" type="submit">
                    Kirim Penawaran
                </button>
            </form>
        </div>
    @endif
@endauth

<h2>Daftar Penawaran</h2>

@forelse ($project->bids as $bid)
    <div class="card">
        <h3>{{ $bid->developer->nama }}</h3>

        <p>
            <b>Harga:</b>
            Rp {{ number_format($bid->harga, 0, ',', '.') }}
        </p>

        <p><b>Durasi:</b> {{ $bid->durasi }} hari</p>
        <p style="white-space: pre-wrap">{{ $bid->proposal }}</p>

        @if ($bid->is_winner)
            <p class="success"><b>Pemenang Lelang</b></p>
        @endif

        @auth
                        @if (
                auth()->user()->role === 'developer'
                && auth()->id() === $bid->developer_id
                && $project->status === 'terbuka'
                && !$project->batas_lelang->isBefore(today())
            )
                <a class="btn"
                   href="{{ route('bids.edit', [$project, $bid]) }}">
                    Edit Penawaran
                </a>

                <form method="POST"
                      action="{{ route('bids.destroy', [$project, $bid]) }}"
                      style="display:inline">
                    @csrf
                    @method('DELETE')

                    <button class="btn" type="submit">
                        Hapus Penawaran
                    </button>
                </form>
            @endif

                        @if (
                auth()->user()->role === 'klien'
                && auth()->id() === $project->client_id
                && $project->status === 'terbuka'
                && !$project->bids->contains('is_winner', true)
            )
                <form method="POST"
                      action="{{ route('bids.winner', [$project, $bid]) }}"
                      onsubmit="return confirm('Pilih developer ini sebagai pemenang?')">
                    @csrf

                    <button class="btn" type="submit">
                        Pilih sebagai Pemenang
                    </button>
                </form>
            @endif
        @endauth
    </div>
@empty
    <div class="card">
        <p>Belum ada penawaran.</p>
    </div>
@endforelse

<a href="{{ route('projects.index') }}">
    Kembali ke Daftar Proyek
</a>
@endsection
