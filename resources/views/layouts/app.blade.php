
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'SIPADU' }} · SIPADU UPNVJ</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/sipadu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sipadu-theme.css') }}">
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
    </script>

</body>
</html>
