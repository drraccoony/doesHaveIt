<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DoesValterHaveIt?')</title>

    {{-- Open Graph --}}
    <meta property="og:site_name" content="DoesValterHaveIt?">
    <meta property="og:title" content="@yield('og_title', 'DoesValterHaveIt?')">
    <meta property="og:description" content="@yield('og_description', 'Check whether Valter owns a game on Steam.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            background: #1b2838;
            color: #c6d4df;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .nav-link {
            position: fixed;
            top: 1.25rem;
            right: 1.5rem;
            font-family: Arial, sans-serif;
            font-size: 0.95rem;
            color: #66c0f4;
            text-decoration: none;
            background: #2a475e;
            border: 1px solid #4891b5;
            padding: 0.4rem 0.9rem;
            border-radius: 4px;
            transition: background 0.15s;
        }
        .nav-link:hover { background: #1e3d56; }

        @yield('styles')
    </style>
</head>
<body>
    @yield('nav')

    @yield('content')
</body>
</html>
