
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'SIPADU' }} · SIPADU UPNVJ</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/sipadu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sipadu-theme.css') }}">

    <style>
        /* Satu keluarga font untuk seluruh SIPADU: landing, auth, sidebar, topbar, dan konten */
        html, body, body *, body *::before, body *::after {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        /* Ikon berbasis SVG tetap dirender sebagai ikon, bukan glyph font */
        svg, svg * { font-family: inherit !important; }
    </style>
</head>

<body>

    @if (session('success'))
        <div class="flash success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="flash error">
            {{ $errors->first() }}
        </div>
    @endif

    {{ $slot ?? '' }}

    @yield('content')

    <script>
        document.querySelectorAll('[data-confirm]').forEach(el => {
            el.addEventListener('click', e => {
                if (!confirm(el.dataset.confirm)) {
                    e.preventDefault();
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.flash').forEach(function (notif) {
                setTimeout(function () {
                    notif.style.transition = 'opacity 0.5s ease';
                    notif.style.opacity = '0';

                    setTimeout(function () {
                        notif.remove();
                    }, 500);
                }, 3000);
            });
        });
    </script>

</body>
</html>
