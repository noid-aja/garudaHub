<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-store">
    <title>Garuda-Hub</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f5f7;
            color: #242424;
        }
        header {
            background: #8b1830;
            padding: 20px;
        }
        header a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 22px;
        }
        main {
            max-width: 950px;
            margin: 30px auto;
            padding: 0 18px;
        }
        .card {
            background: white;
            padding: 22px;
            margin-bottom: 16px;
            border-radius: 10px;
        }
        .btn {
            display: inline-block;
            padding: 9px 14px;
            background: #8b1830;
            color: white;
            border: 0;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        input, textarea, select {
            box-sizing: border-box;
            width: 100%;
            padding: 10px;
            margin: 7px 0 15px;
        }
        label { font-weight: bold; }
        .success { color: green; }
        .error { color: #b42318; }
    </style>
</head>
<body>
    @if (!request()->routeIs('login', 'register'))
        <header>
            <a href="{{ route('projects.index') }}">Garuda-Hub</a>

            @auth
                <a href="{{ route('dashboard.index') }}"
                   style="font-size:16px; margin-left:20px">Dashboard</a>

                <span style="color:white; margin-left:20px">
                    {{ auth()->user()->nama }} — {{ auth()->user()->email }}
                    ({{ auth()->user()->role }})
                </span>

                <form method="POST" action="{{ route('logout') }}"
                      style="display:inline; margin-left:20px">
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

    <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</body>
</html>
