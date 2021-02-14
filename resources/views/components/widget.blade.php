@props([
    'title',
    'columns' => 1,
    'uuid' => Str::uuid(),
    'settings' => null,
])

<article 
    class="col-span-1 lg:col-span-{{ $columns }}"
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