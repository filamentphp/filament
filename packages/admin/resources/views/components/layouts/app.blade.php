<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ trans('filament::layout.direction') === 'rtl' ? 'rtl' : 'ltr' }}" class="antialiased bg-gray-100 js-focus-visible">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? "{$title} - " : null }} {{ config('app.name') }}</title>

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
        <div class="min-h-screen bg-white text-gray-800 font-sans antialiased grid grid-cols-[20rem,1fr]">
            <aside class="bg-gray-100 grid grid-rows-[4rem,1fr,auto]">
                <header class="border-b px-6 flex items-center">
                    <a class="text-xl font-bold" href="">
                        {{ config('app.name') }}
                    </a>
                </header>

                <nav class="py-6">
                    <ul class="space-y-6 px-6">
                        @foreach (\Filament\Facades\Filament::getNavigation() as $group => $items)
                            <li>
                                @if ($group)
                                    <p class="font-bold uppercase text-gray-600 text-xs tracking-wider">
                                        {{ $group }}
                                    </p>
                                @endif

                                <ul @class([
                                    'text-sm space-y-1 -mx-3',
                                    'mt-2' => $group,
                                ])>
                                    @foreach ($items as $item)
                                        <li>
                                            <a
                                                href="{{ $item->getUrl() }}"
                                                @class([
                                                    'flex items-center gap-3 px-3 py-2 rounded-md font-medium',
                                                    'bg-primary-500 text-white' => $item->isActive(),
                                                ])
                                            >
                                                <x-dynamic-component :component="$item->getIcon()" class="h-5 w-5" />

                                                <span>
                                                    {{ $item->getLabel() }}
                                                </span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>

                            @if (! $loop->last)
                                <li>
                                    <div class="border-t -mr-6"></div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </nav>

                <footer class="border-t px-6 py-3 flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-gray-200"></div>

                    <div>
                        <p class="text-sm font-bold">
                            {{ auth()->user()->name }}
                        </p>

                        <p class="text-xs text-gray-500">
                            Sign out
                        </p>
                    </div>
                </footer>
            </aside>

            <main class="grid grid-rows-[4rem,1fr] relative shadow-lg">
                <header class="flex items-center border-b px-6">
                    <ul class="flex gap-4 items-center font-medium text-sm">
                        <li>
                            <a href="" id="">Articles</a>
                        </li>

                        <li class="h-6 border-r -skew-x-12"></li>

                        <li>
                            <a href="" id="">why-inertia-js-is-goat</a>
                        </li>

                        <li class="h-6 border-r -skew-x-12"></li>

                        <li>
                            <a class="text-gray-500" href="" id="">Edit</a>
                        </li>
                    </ul>
                </header>

                {{ $slot }}
            </main>
        </div>

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
    </body>
</html>
