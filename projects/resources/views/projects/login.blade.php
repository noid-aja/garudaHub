@extends('layouts.app')

@section('content')
<div class="card">
    <h1>Login Garuda-Hub</h1>

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}"
               autocomplete="email" required>

        <label>Password</label>
        <div class="password-wrapper">
            <input type="password" name="password" id="loginPassword"
                   autocomplete="current-password" class="password-input" required>
            <button type="button" id="lihatPassword"
                    class="password-toggle-button">
                Lihat
            </button>
        </div>

        <button class="btn" type="submit">Login</button>
    </form>

    <p class="auth-footer">
        Belum punya akun?
        <a href="{{ route('register') }}">Daftar sekarang</a>
    </p>
</div>

@endsection
