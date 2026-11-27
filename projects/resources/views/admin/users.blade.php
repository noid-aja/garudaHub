@extends('layouts.app')

@section('content')
<h1>Kelola Pengguna</h1>

<div class="card">
    <p>Perubahan role dan penghapusan pengguna hanya bisa dilakukan admin.</p>
    <a href="{{ route('dashboard.admin') }}">Kembali ke Dashboard Admin</a>
</div>

@forelse ($users as $user)
    <div class="card">
        <h3>{{ $user->nama }}</h3>
        <p>{{ $user->email }}</p>
        <p>Role sekarang: <b>{{ ucfirst($user->role) }}</b></p>

        @if (auth()->id() === $user->id)
            <p>Ini akun admin yang sedang digunakan.</p>
        @else
            <form method="POST" action="{{ route('admin.users.role', $user) }}">
                @csrf
                @method('PATCH')

                <label>Ubah Role</label>
                <select name="role">
                    @foreach (['klien', 'developer', 'admin'] as $role)
                        <option value="{{ $role }}" @selected($user->role === $role)>
                            {{ ucfirst($role) }}
                        </option>
                    @endforeach
                </select>

                <button class="btn" type="submit">Simpan Role</button>
            </form>

            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                  data-confirm="Hapus pengguna beserta proyek dan penawarannya?">
                @csrf
                @method('DELETE')
                <button class="btn" type="submit">Hapus Pengguna</button>
            </form>
        @endif
    </div>
@empty
    <p>Belum ada pengguna.</p>
@endforelse

{{ $users->links('pagination.simple') }}
@endsection
