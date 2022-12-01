@props([
    'after' => null,
    'heading' => null,
    'subheading' => null,
])

<x-filament::layouts.base :livewire="$livewire">
    <div class="filament-card-layout flex items-center justify-center min-h-screen bg-gray-100 text-gray-900 py-14 dark:bg-gray-900 dark:text-white">
        <div @class([
            'w-screen px-6 space-y-8 md:mt-0 md:px-2',
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
        ])>
            <div class="filament-card-layout-card relative space-y-4 rounded-xl bg-white/50 p-8 shadow-2xl ring-1 ring-gray-900/10 backdrop-blur-xl dark:bg-gray-900/50 dark:ring-gray-50/10">
                @if ($livewire->hasBrand())
                    <div class="flex justify-center w-full">
                        <x-filament::brand />
                    </div>
                @endif

                <div class="space-y-2">
                    @if (filled($heading ??= $livewire->getHeading()))
                        <h2 class="text-2xl font-bold tracking-tight text-center">
                            {{ $heading }}
                        </h2>
                    @endif

                    @if (filled($subheading ??= $livewire->getSubHeading()))
                        <h3 class="text-sm text-gray-600 font-medium tracking-tight text-center dark:text-gray-300">
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
            <div class="absolute top-0 right-0 flex items-center justify-end p-2 w-full">
                @livewire('filament.core.notifications')

                <x-filament::user-menu />
            </div>
        @endif
    </div>

    @if (! filament()->auth()->check())
        @livewire('filament.core.notifications')
    @endif
</x-filament::layouts.base>
