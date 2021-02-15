@props([
    'title',
    'columns' => 1,
    'uuid' => Str::uuid(),
    'settings' => null,
])

@php
    $columnsClasses = [
        '',
        'col-span-1 lg:col-span-1',
        'col-span-1 lg:col-span-2',
        'col-span-1 lg:col-span-3',
        'col-span-1 lg:col-span-4',
        'col-span-1 lg:col-span-5',
        'col-span-1 lg:col-span-6',
        'col-span-1 lg:col-span-7',
        'col-span-1 lg:col-span-8',
        'col-span-1 lg:col-span-9',
        'col-span-1 lg:col-span-10',
        'col-span-1 lg:col-span-11',
        'col-span-1 lg:col-span-12',
    ][$columns];
@endphp

<article
    class="{{ $columnsClasses }}"
    {{ $attributes->merge([
        'aria-labelledby' => 'widget-heading-'.$uuid,
    ])->except('class') }}
>
    <x-filament::card
        {{ $attributes->merge([
            'class' => 'space-y-2',
        ])->only('class') }}
    >
        <div class="flex items-start justify-between space-x-4">
            @if ($title)
                <h2 id="widget-heading-{{ $uuid }}" class="font-medium leading-tight">{{ $title }}</h2>
            @endif

            @if ($settings)
                <x-filament::dropdown class="flex text-gray-400 hover:text-current transition-colors duration-200">
                    <x-slot name="button">
                        <span class="sr-only">{{ __('filament::widgets.settings') }}</span>
                        <x-heroicon-o-cog class="w-4 h-4" aria-hidden="true" />
                    </x-slot>

                    {{ $settings }}
                </x-filament::dropdown>
            @endif
        </div>
        <div>
            {{ $slot }}
        </div>
    </x-filament::card>
</article>
