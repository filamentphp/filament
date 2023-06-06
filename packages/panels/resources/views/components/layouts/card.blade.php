@props([
    'after' => null,
    'heading' => null,
    'subheading' => null,
])

<x-filament::layouts.base :livewire="$livewire">
    <div
        class="filament-card-layout flex min-h-screen items-center justify-center py-14"
    >
        <div
            @class([
                'w-screen space-y-8 px-6 md:mt-0 md:px-2',
                match ($width = $livewire->getCardWidth()) {
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
                    default => $width,
                },
            ])
        >
            <div
                class="filament-card-layout-card relative space-y-4 rounded-xl bg-white/50 p-8 shadow-2xl ring-1 ring-gray-950/5 backdrop-blur-xl dark:bg-gray-900/50 dark:ring-white/20"
            >
                @if ($livewire->hasLogo())
                    <div class="flex w-full justify-center">
                        <x-filament::logo />
                    </div>
                @endif

                <div class="space-y-2">
                    @if (filled($heading ??= $livewire->getHeading()))
                        <h2
                            class="text-center text-2xl font-bold tracking-tight"
                        >
                            {{ $heading }}
                        </h2>
                    @endif

                    @if (filled($subheading ??= $livewire->getSubHeading()))
                        <h3
                            class="text-center text-sm font-medium tracking-tight text-gray-600 dark:text-gray-300"
                        >
                            {{ $subheading }}
                        </h3>
                    @endif
                </div>

                <div {{ $attributes }}>
                    {{ $slot }}
                </div>
            </div>

            {{ $after }}
        </div>

        @if (filament()->auth()->check())
            <div
                class="absolute end-0 top-0 flex w-full items-center justify-end p-2"
            >
                @if (filament()->hasDatabaseNotifications())
                    @livewire('filament.core.database-notifications')
                @endif

                <x-filament::user-menu />
            </div>
        @endif
    </div>
</x-filament::layouts.base>
