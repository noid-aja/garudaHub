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
        <div style="position:relative">
            <input type="password" name="password" id="loginPassword"
                   autocomplete="current-password" style="padding-right:130px" required>
            <button type="button" id="lihatPassword"
                    style="position:absolute; right:8px; top:7px; padding:8px; cursor:pointer">
                Lihat
            </button>
        </div>

        <button class="btn" type="submit">Login</button>
    </form>

    <p style="text-align:center; margin-top:20px">
        Belum punya akun?
        <a href="{{ route('register') }}">Daftar sekarang</a>
    </p>
</div>

<script>
    const passwordLogin = document.getElementById('loginPassword');
    const tombolLihat = document.getElementById('lihatPassword');

    tombolLihat.addEventListener('click', function () {
        const terlihat = passwordLogin.type === 'text';
        passwordLogin.type = terlihat ? 'password' : 'text';
        tombolLihat.textContent = terlihat ? 'Lihat' : 'Sembunyikan';
    });
</script>
@endsection
