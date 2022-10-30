@props([
    'title' => null,
    'width' => 'md',
])

<x-filament::layouts.base :title="$title">
    <div class="filament-login-page flex items-center justify-center min-h-screen bg-gray-100 text-gray-900 py-12 dark:bg-gray-900 dark:text-white">
        <div @class([
            'w-screen px-6 -mt-16 space-y-8 md:mt-0 md:px-2',
            match($width) {
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
            <div class="relative space-y-4 rounded-xl bg-white/50 p-8 shadow-2xl ring-1 ring-gray-900/10 backdrop-blur-xl dark:bg-gray-900/50 dark:ring-gray-50/10">
                <div class="flex justify-center w-full">
                    <x-filament::brand />
                </div>

                @if (filled($title))
                    <h2 class="text-2xl font-bold tracking-tight text-center">
                        {{ $title }}
                    </h2>
                @endif

                <div {{ $attributes }}>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>

    @livewire('notifications')
</x-filament::layouts.base>
