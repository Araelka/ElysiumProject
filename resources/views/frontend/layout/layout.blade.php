<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<title>@yield('title', 'Главная страница')</title>
<link rel="stylesheet" href="{{ asset('css/header.css') }}">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
<link rel="preload" href="/images/disco-background.jpg" as="image">
<script src="{{ asset('js/script.js') }}"></script>
</head>
<body>
    @include('frontend.layout.header')
    
    <main>
        @yield('content')
    </main>

    @include('frontend.layout.footer')
</body>
</html>

