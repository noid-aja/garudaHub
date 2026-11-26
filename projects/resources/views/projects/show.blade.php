
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
        <b>Batas lelang:</b>
        {{ $project->batas_lelang->format('d-m-Y') }}
    </p>

    <p><b>Status:</b> {{ ucfirst($project->status) }}</p>

    <a class="btn" href="{{ route('projects.edit', $project) }}">
        Edit Proyek
    </a>

    <form action="{{ route('projects.destroy', $project) }}"
          method="POST" style="display:inline">
        @csrf
        @method('DELETE')

        <button class="btn" type="submit">
            Hapus Proyek
        </button>
    </form>
</div>

@if ($errors->any())
    <div class="card error">
        <h3>Terjadi kesalahan:</h3>

        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

@if (
    $project->status === 'terbuka'
    && !$project->batas_lelang->isBefore(today())
)
    <div class="card">
        <h2>Ajukan Penawaran</h2>

        <form method="POST"
              action="{{ route('bids.store', $project) }}">
            @csrf

            <label>Developer</label>
            <select name="developer_id" required>
                <option value="">Pilih Developer</option>

                @foreach ($developers as $developer)
                    <option value="{{ $developer->id }}"
                        @selected(old('developer_id') == $developer->id)>
                        {{ $developer->nama }}
                    </option>
                @endforeach
            </select>

            <label>Harga Penawaran (Rp)</label>
            <input type="number"
                   name="harga"
                   min="1"
                   value="{{ old('harga') }}"
                   required>

            <label>Durasi Pengerjaan (hari)</label>
            <input type="number"
                   name="durasi"
                   min="1"
                   value="{{ old('durasi') }}"
                   required>

            <label>Proposal</label>
            <textarea name="proposal"
                      maxlength="5000"
                      required>{{ old('proposal') }}</textarea>

            <button class="btn" type="submit">
                Kirim Penawaran
            </button>
        </form>
    </div>
@else
    <div class="card">
        <p>Lelang sudah ditutup atau melewati batas waktu.</p>
    </div>
@endif

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

        @if (
            $project->status === 'terbuka'
            && !$project->batas_lelang->isBefore(today())
            && !$bid->is_winner
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

                <button type="submit" class="btn">
                    Hapus Penawaran
                </button>
            </form>
        @endif
    </div>
@empty
    <div class="card">
        <p>Belum ada penawaran untuk proyek ini.</p>
    </div>
@endforelse

<a href="{{ route('projects.index') }}">
    Kembali ke Daftar Proyek
</a>
@endsection
