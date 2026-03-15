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
    @hasSection('og_image')
    <meta property="og:image" content="@yield('og_image')">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    @endif

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

        .nav-links {
            position: fixed;
            top: 1.25rem;
            right: 1.5rem;
            display: flex;
            gap: 0.5rem;
        }

        .nav-link {
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

        footer {
            position: fixed;
            bottom: 1rem;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 0.78rem;
            color: #4a6174;
            font-family: Arial, sans-serif;
        }
        footer a {
            color: #4a6174;
            text-decoration: none;
        }
        footer a:hover { color: #8cb4d0; }

        @yield('styles');
    </style>
</head>
<body>
    <div class="nav-links">
        @yield('nav')
    </div>

    @yield('content')

    <footer>
        Violating Valter's Privacy with ♥ by <a href="https://github.com/drraccoony/doesHaveIt" target="_blank" rel="noopener">Rico</a> using Laravel
    </footer>
</body>
</html>
