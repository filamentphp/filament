<x-filament-panels::layout.base :livewire="$livewire">
    @props([
        'after' => null,
        'heading' => null,
        'subheading' => null,
    ])

    <div class="fi-simple-layout flex min-h-screen flex-col items-center">
        @if (filament()->auth()->check())
            <div
                class="absolute end-0 top-0 flex h-16 items-center gap-x-4 pe-4 md:pe-6 lg:pe-8"
            >
                @if (filament()->hasDatabaseNotifications())
                    @livewire(Filament\Livewire\DatabaseNotifications::class, ['lazy' => true])
                @endif

                <x-filament-panels::user-menu />
            </div>
        @endif

        <div
            class="fi-simple-main-ctn flex w-full flex-grow items-center justify-center"
        >
            <main
                @class([
                    'fi-simple-main my-16 w-full bg-white px-6 py-12 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:rounded-xl sm:px-12',
                    match ($maxWidth ?? null) {
                        'xs' => 'sm:max-w-xs',
                        'sm' => 'sm:max-w-sm',
                        'md' => 'sm:max-w-md',
                        'lg' => 'sm:max-w-lg',
                        'xl' => 'sm:max-w-xl',
                        '2xl' => 'sm:max-w-2xl',
                        '3xl' => 'sm:max-w-3xl',
                        '4xl' => 'sm:max-w-4xl',
                        '5xl' => 'sm:max-w-5xl',
                        '6xl' => 'sm:max-w-6xl',
                        '7xl' => 'sm:max-w-7xl',
                        default => 'sm:max-w-lg',
                    },
                ])
            >
                {{ $slot }}
            </main>
        </div>

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::footer') }}
    </div>
</x-filament-panels::layout.base>
