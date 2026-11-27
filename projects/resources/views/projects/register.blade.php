@extends('layouts.app')

@section('content')
<div class="card">
    <h1>Daftar Garuda-Hub</h1>

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <label>Nama Lengkap</label>
        <input type="text" name="nama" maxlength="50"
               value="{{ old('nama') }}" required>

        <label>Email</label>
        <input type="email" name="email" maxlength="100"
               value="{{ old('email') }}" required>

        <label>Daftar Sebagai</label>
        <select name="role" required>
            <option value="klien" @selected(old('role') === 'klien')>Klien</option>
            <option value="developer" @selected(old('role') === 'developer')>Developer</option>
        </select>

        <label>Password</label>
        <p>Minimal 12 karakter, mengandung huruf besar, huruf kecil, angka, dan simbol.</p>
        <input type="password" name="password" id="registerPassword"
               autocomplete="new-password" minlength="12" required>

        <label>Konfirmasi Password</label>
        <input type="password" name="password_confirmation" id="confirmPassword"
               autocomplete="new-password" minlength="12" required>

        <label class="password-toggle-label">
            <input type="checkbox" id="tampilPassword" class="password-toggle-checkbox">
            Tampilkan password
        </label>

        <button class="btn" type="submit">Buat Akun</button>
    </form>

    <p class="auth-footer">
        Sudah punya akun? <a href="{{ route('login') }}">Login</a>
    </p>
</div>

@endsection
