<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-gray-200 antialiased js-focus-visible">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} | {{ config('app.name') }}</title>

    @livewireStyles
    @filamentStyles
    @stack('head')
</head>

<body class="text-gray-700 font-sans">
    {{ $slot }}

    <x-filament::notification />

    @livewireScripts
    @filamentScripts
    @stack('js')
</body>
</html>
