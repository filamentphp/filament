<x-filament-panels::layout.base :livewire="$livewire">
    @props([
        'after' => null,
        'heading' => null,
        'subheading' => null,
    ])

    <div class="fi-simple-layout flex min-h-screen items-center">
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

        <div class="fi-simple-main-ctn w-full">
            <main
                class="fi-simple-main mx-auto my-16 w-full bg-white px-6 py-12 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:max-w-lg sm:rounded-xl sm:px-12"
            >
                {{ $slot }}
            </main>
        </div>

        {{ \Filament\Support\Facades\FilamentView::renderHook('panels::footer') }}
    </div>
</x-filament-panels::layout.base>
