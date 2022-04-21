@props([
    'title' => null,
])

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ __('filament::layout.direction') ?? 'ltr' }}"
    class="filament antialiased bg-gray-100 js-focus-visible"
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @foreach (\Filament\Facades\Filament::getMeta() as $tag)
            {{ $tag }}
        @endforeach

        @if ($favicon = config('filament.favicon'))
            <link rel="icon" href="{{ $favicon }}">
        @endif

        <title>{{ $title ? "{$title} - " : null }} {{ config('app.name') }}</title>

        <style>
            [x-cloak=""], [x-cloak="1"] { display: none !important; }
            @media (max-width: 1023px) { [x-cloak="-lg"] { display: none !important; } }
            @media (min-width: 1024px) { [x-cloak="lg"] { display: none !important; } }
        </style>

        @livewireStyles

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&display=swap" rel="stylesheet" />

        @foreach (\Filament\Facades\Filament::getStyles() as $name => $path)
            @if (Str::of($path)->startsWith(['http://', 'https://']))
                <link rel="stylesheet" href="{{ $path }}" />
            @else
                <link rel="stylesheet" href="{{ route('filament.asset', [
                    'file' => "{$name}.css",
                ]) }}" />
            @endif
        @endforeach

        <link rel="stylesheet" href="{{ \Filament\Facades\Filament::getThemeUrl() }}" />

        @if (config('filament.dark_mode'))
            <script>
                const theme = localStorage.getItem('theme')

                if ((theme === 'dark') || (! theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark')
                }
            </script>
        @endif
    </head>

    <body @class([
        'bg-gray-100 text-gray-900 filament-body',
        'dark:text-gray-100 dark:bg-gray-900' => config('filament.dark_mode'),
    ])>
        {{ $slot }}

        @livewireScripts

        <script>
            window.filamentData = @json(\Filament\Facades\Filament::getScriptData());
        </script>

        @foreach (\Filament\Facades\Filament::getBeforeCoreScripts() as $name => $path)
            @if (Str::of($path)->startsWith(['http://', 'https://']))
                <script src="{{ $path }}"></script>
            @else
                <script src="{{ route('filament.asset', [
                    'file' => "{$name}.js",
                ]) }}"></script>
            @endif
        @endforeach

        @stack('beforeCoreScripts')

        <script src="{{ route('filament.asset', [
            'id' => Filament\get_asset_id('app.js'),
            'file' => 'app.js',
        ]) }}"></script>

        @foreach (\Filament\Facades\Filament::getScripts() as $name => $path)
            @if (Str::of($path)->startsWith(['http://', 'https://']))
                <script src="{{ $path }}"></script>
            @else
                <script src="{{ route('filament.asset', [
                    'file' => "{$name}.js",
                ]) }}"></script>
            @endif
        @endforeach

        @stack('scripts')
    </body>
</html>
