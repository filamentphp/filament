@php
    $heading = $this->getHeading();
    $description = $this->getDescription();
    $filters = $this->getFilters();
@endphp

<x-filament-widgets::widget class="fi-wi-chart-widget">
    <x-filament::card class="grid gap-y-4">
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
                        class="ml-auto"
                    >
                        <x-filament::input.select wire:model.live="filter">
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
                    x-ref="backgroundColorElement"
                    class="text-gray-700 dark:text-gray-200"
                ></span>

                <span
                    x-ref="borderColorElement"
                    class="text-gray-950/50 dark:text-white/60"
                ></span>
            </div>
        </div>
    </x-filament::card>
</x-filament-widgets::widget>
