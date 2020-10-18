<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-gray-100 antialiased js-focus-visible">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        @filamentStyles
        @livewireStyles
        @stack('head')
    </head>
    <body class="text-gray-800">
        @yield('content')
        @filamentScripts
        @livewireScripts
        @stack('scripts')
    </body>
</html>