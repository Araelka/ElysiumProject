<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Главная страница')</title>
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    @include('frontend.layout.header')
    
    <main>
        @yield('content')
    </main>

    @include('frontend.layout.footer')
</body>
</html>