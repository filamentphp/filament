@php
    $heading = $this->getHeading();
    $subheading = $this->getSubheading();
    $filters = $this->getFilters();
@endphp

<x-filament-widgets::widget class="filament-widgets-chart-widget">
    <x-filament::card>
        @if ($heading || $filters)
            <div class="flex items-center justify-between gap-8">
                <x-filament::card.header>
                    @if ($heading)
                        <x-filament::card.heading>
                            {{ $heading }}
                        </x-filament::card.heading>
                    @endif

                    @if ($subheading)
                        <x-filament::card.subheading>
                            {{ $subheading }}
                        </x-filament::card.subheading>
                    @endif
                </x-filament::card.header>

                @if ($filters)
                    <div class="flex items-center gap-3">
                        @if ($hasFilterLoadingIndicator)
                            <x-filament::loading-indicator
                                class="h-8 w-8 text-gray-500"
                                wire:loading
                                wire:target="filter"
                            />
                        @endif

                        <select
                            class="block h-10 rounded-lg border-gray-300 text-gray-900 shadow-sm outline-none transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:focus:border-primary-500"
                            wire:model="filter"
                            wire:loading.class="animate-pulse"
                        >
                            @foreach ($filters as $value => $label)
                                <option value="{{ $value }}">
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        @endif

        <div
            @if ($pollingInterval = $this->getPollingInterval()) wire:poll.{{ $pollingInterval }}="updateChartData" @endif
        >
            <div
                x-ignore
                ax-load
                ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                x-data="chart({
                            cachedData: @js($this->getCachedData()),
                            options: @js($this->getOptions()),
                            type: @js($this->getType()),
                        })"
                wire:ignore
            >
                <canvas
                    x-ref="canvas"
                    @if ($maxHeight = $this->getMaxHeight())
                        style="max-height: {{ $maxHeight }}"
                    @endif
                ></canvas>

                <span
                    x-ref="backgroundColorElement"
                    class="text-gray-50 dark:text-gray-300"
                ></span>

                <span
                    x-ref="borderColorElement"
                    class="text-gray-500 dark:text-gray-200"
                ></span>
            </div>
        </div>
    </x-filament::card>
</x-filament-widgets::widget>
