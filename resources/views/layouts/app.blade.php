<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') &bull; {{ config('app.name') }}</title>
    @livewireStyles
    @filamentAssets
    @stack('head')
</head>
<body class="font-sans antialiased text-gray-900 dark:text-gray-50
    @isset($classes['body']) 
        {{ $classes['body'] }}
    @endisset
">
    <a href="#content" class="sr-only">Skip to content</a>
    @yield('content')
    @stack('footer')
    @include('filament::partials.notification')
    @livewireScripts
    @stack('scripts')
</body>
</html>