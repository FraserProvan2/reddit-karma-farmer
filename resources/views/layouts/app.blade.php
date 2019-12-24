<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>sb-23</title>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <main class="py-4">
            <div class="container">
                @include('layouts.navbar')
                @yield('content')

                <div class="small text-muted text-center w-100">Fraser Provan | 2019</div>
            </div>
        </main>
    </div>
    
    <script src="{{ mix('js/app.js') }}" defer></script>
    <script src="https://use.fontawesome.com/c991091b95.js"></script>
</body>
</html>
