<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-store">
    <title>Garuda-Hub</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    
</head>
<body>
    @if (!request()->routeIs('login', 'register'))
        <header>
            <a href="{{ route('projects.index') }}">Garuda-Hub</a>

            @auth
                <a href="{{ route('dashboard.index') }}"
                   class="dashboard-link">Dashboard</a>

                <span class="user-summary">
                    {{ auth()->user()->nama }} — {{ auth()->user()->email }}
                    ({{ auth()->user()->role }})
                </span>

                <form method="POST" action="{{ route('logout') }}"
                      class="logout-form">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            @endauth
        </header>
    @endif

    <main>
        @if (session('success'))
            <p class="success">{{ session('success') }}</p>
        @endif

        @yield('content')
    </main>

    </body>
</html>
