@php
    $heading = $this->getHeading();
    $description = $this->getDescription();
    $filters = $this->getFilters();
@endphp

<x-filament-widgets::widget>
    <x-filament::card class="fi-wi-chart grid gap-y-4">
        @if ($heading || $description || $filters)
            <div class="flex items-center gap-x-4">
                @if ($heading || $description)
                    <div class="grid gap-y-1">
                        @if ($heading)
                            <h3 class="text-base font-semibold leading-6">
                                {{ $heading }}
                            </h3>
                        @endif

                        @if ($description)
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $description }}
                            </p>
                        @endif
                    </div>
                @endif

                @if ($filters)
                    <x-filament-forms::affixes
                        inline-prefix
                        wire:target="filter"
                        class="ms-auto"
                    >
                        <x-filament::input.select
                            inline-prefix
                            wire:model.live="filter"
                        >
                            @foreach ($filters as $value => $label)
                                <option value="{{ $value }}">
                                    {{ $label }}
                                </option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament-forms::affixes>
                @endif
            </div>
        @endif

        <div
            @if ($pollingInterval = $this->getPollingInterval())
                wire:poll.{{ $pollingInterval }}="updateChartData"
            @endif
        >
            <div
                ax-load
                ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                wire:ignore
                x-data="chart({
                            cachedData: @js($this->getCachedData()),
                            options: @js($this->getOptions()),
                            type: @js($this->getType()),
                        })"
                x-ignore
            >
                <canvas
                    x-ref="canvas"
                    @if ($maxHeight = $this->getMaxHeight())
                        style="max-height: {{ $maxHeight }}"
                    @endif
                ></canvas>

                <span
                    x-ref="colorElement"
                    @class([
                        match ($color = $this->getColor()) {
                            'gray' => 'text-gray-400',
                            default => 'text-custom-500 dark:text-custom-400',
                        },
                    ])
                    style="{{ \Filament\Support\get_color_css_variables($color, shades: [400, 500]) }}"
                ></span>

                <span
                    x-ref="gridColorElement"
                    class="text-gray-200 dark:text-gray-800"
                ></span>

                <span
                    x-ref="textColorElement"
                    class="text-gray-500 dark:text-gray-400"
                ></span>
            </div>
        </div>
    </x-filament::card>
</x-filament-widgets::widget>
