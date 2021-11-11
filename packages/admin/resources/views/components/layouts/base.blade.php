@props([
    'title' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ trans('filament::layout.direction') === 'rtl' ? 'rtl' : 'ltr' }}" class="antialiased bg-gray-100 js-focus-visible">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? "{$title} - " : null }} {{ config('app.name') }}</title>

        <style>[x-cloak] { display: none !important; }</style>

        @livewireStyles

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&family=JetBrains+Mono&display=swap" rel="stylesheet" />

        <link rel="stylesheet" href="{{ route('filament.asset', [
            'id' => \Filament\get_asset_id('app.css'),
            'path' => 'app.css',
        ]) }}" />

        @foreach (\Filament\Facades\Filament::getStyles() as $path)
            <link rel="stylesheet" href="{{ $path }}" />
        @endforeach
    </head>

    <body>
        {{ $slot }}

        @livewireScripts

        <script>
            window.filamentData = @json(\Filament\Facades\Filament::getScriptData());
        </script>

        <script src="{{ route('filament.asset', [
            'id' => Filament\get_asset_id('app.js'),
            'path' => 'app.js',
        ]) }}"></script>

        @foreach (\Filament\Facades\Filament::getScripts() as $path)
            <script src="{{ $path }}"></script>
        @endforeach

        @stack('scripts')
    </body>
</html>
