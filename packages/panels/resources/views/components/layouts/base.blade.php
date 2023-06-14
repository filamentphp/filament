@props([
    'livewire',
])

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ __('filament::layout.direction') ?? 'ltr' }}"
    @class([
        'filament js-focus-visible min-h-screen antialiased',
        'dark' => filament()->hasDarkModeForced(),
    ])
>
    <head>
        {{ filament()->renderHook('head.start') }}

        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        @if ($favicon = filament()->getFavicon())
            <link rel="icon" href="{{ $favicon }}" />
        @endif

        <title>
            {{ filled($title = $livewire->getTitle()) ? "{$title} - " : null }}
            {{ filament()->getBrandName() }}
        </title>

        {{ filament()->renderHook('styles.start') }}

        <style>
            [x-cloak=''],
            [x-cloak='x-cloak'],
            [x-cloak='1'] {
                display: none !important;
            }

            @media (max-width: 1023px) {
                [x-cloak='-lg'] {
                    display: none !important;
                }
            }

            @media (min-width: 1024px) {
                [x-cloak='lg'] {
                    display: none !important;
                }
            }
        </style>

        @livewireStyles
        @filamentStyles
        {{ filament()->getTheme()->getHtml() }}
        {{ filament()->getFontHtml() }}

        <style>
            :root {
                --font-family: {!! filament()->getFontFamily() !!};
                --filament-widgets-chart-font-family: var(--font-family);
                --sidebar-width: {{ filament()->getSidebarWidth() }};
                --collapsed-sidebar-width: {{ filament()->getCollapsedSidebarWidth() }};
            }
        </style>

        {{ filament()->renderHook('styles.end') }}

        @if (filament()->hasDarkMode() && (! filament()->hasDarkModeForced()))
            <script>
                const theme = localStorage.getItem('theme') ?? 'system'

                if (
                    theme === 'dark' ||
                    (theme === 'system' &&
                        window.matchMedia('(prefers-color-scheme: dark)')
                            .matches)
                ) {
                    document.documentElement.classList.add('dark')
                }
            </script>
        @endif

        {{ filament()->renderHook('head.end') }}
    </head>

    <body
        class="filament-body min-h-screen overscroll-y-none bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-white"
    >
        {{ filament()->renderHook('body.start') }}

        {{ $slot }}

        @livewire('filament.core.notifications')

        {{ filament()->renderHook('scripts.start') }}

        @livewireScripts
        @filamentScripts(withCore: true)

        @if (config('filament.broadcasting.echo'))
            <script>
                window.addEventListener('DOMContentLoaded', () => {
                    window.Echo = new window.EchoFactory(@js(config('filament.broadcasting.echo')))

                    window.dispatchEvent(new CustomEvent('EchoLoaded'))
                })
            </script>
        @endif

        @stack('scripts')

        {{ filament()->renderHook('scripts.end') }}

        {{ filament()->renderHook('body.end') }}
    </body>
</html>
