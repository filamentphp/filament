<x-filament-panels::layout.base :livewire="$livewire">
    @props([
        'after' => null,
        'heading' => null,
        'subheading' => null,
    ])

    <div class="flex flex-col items-center min-h-screen fi-simple-layout">
        @if (filament()->auth()->check())
            <div
                class="absolute top-0 flex items-center h-16 end-0 gap-x-4 pe-4 md:pe-6 lg:pe-8"
            >
                @if (filament()->hasDatabaseNotifications())
                    @livewire(Filament\Livewire\DatabaseNotifications::class, ['lazy' => true])
                @endif

                <x-filament-panels::user-menu />
            </div>
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::header') }}

        <div
            class="flex items-center justify-center flex-grow w-full fi-simple-main-ctn"
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
