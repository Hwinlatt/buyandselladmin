<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('dist/img/b-logo.ico') }}">
        <title>Buy&Sell Messenger</title>
        <link rel="stylesheet" href="{{asset('build/assets/app-1a393faf.css')}}">
        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">
        <link rel="stylesheet" href="{{ asset('dist/css/custom.css') }}">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>
    </body>
    <script src="{{ asset('build/assets/app-40420331.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
</html>
