<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-gray-200 antialiased js-focus-visible">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? null }} {{ $title ?? false ? '|' : null }} {{ config('app.name') }}</title>

    @livewireStyles
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Commissioner:wght@200;300;400;500;600;700&amp;family=JetBrains+Mono:ital@0;1&amp;display=swap">
    <link rel="stylesheet" href="{{ route('filament.asset', ['path' => 'css/filament.css']) }}" />
    <link rel="stylesheet" href="{{ route('filament.asset', ['path' => 'css/filament.css.map']) }}" />
    @stack('filament-styles')
</head>

<body class="text-gray-700">
    {{ $slot }}

    <x-filament::notification />

    @livewireScripts
    <script src="{{ route('filament.asset', ['path' => 'js/filament.js']) }}"></script>
    <script src="{{ route('filament.asset', ['path' => 'js/filament.js.map']) }}"></script>
    @stack('filament-scripts')
</body>
</html>
