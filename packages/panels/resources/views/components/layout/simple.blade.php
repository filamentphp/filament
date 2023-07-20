<x-filament::layout.base :livewire="$livewire">
    @props([
        'after' => null,
        'heading' => null,
        'subheading' => null,
    ])

    <div class="fi-simple-layout flex min-h-full items-center">
        @if (filament()->auth()->check())
            <div
                class="absolute end-0 top-0 flex w-full items-center justify-end p-2"
            >
                @if (filament()->hasDatabaseNotifications())
                    @livewire(Filament\Livewire\DatabaseNotifications::class, ['lazy' => true])
                @endif

                <x-filament::user-menu />
            </div>
        @endif

        <div class="fi-simple-main-ctn w-full">
            <main
                @class([
                    'fi-simple-main mx-auto w-full space-y-4 rounded-xl bg-white p-12 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10',
                    match ($maxContentWidth = $livewire->getMaxContentWidth()) {
                        'xs' => 'max-w-xs',
                        'sm' => 'max-w-sm',
                        'md' => 'max-w-md',
                        'lg' => 'max-w-lg',
                        'xl' => 'max-w-xl',
                        '2xl' => 'max-w-2xl',
                        '3xl' => 'max-w-3xl',
                        '4xl' => 'max-w-4xl',
                        '5xl' => 'max-w-5xl',
                        '6xl' => 'max-w-6xl',
                        '7xl' => 'max-w-7xl',
                        'full' => 'max-w-full',
                        default => $maxContentWidth,
                    },
                ])
            >
                {{ $slot }}
            </main>
        </div>
    </div>
</x-filament::layout.base>
