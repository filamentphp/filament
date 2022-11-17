@php
    $heading = $this->getHeading();
    $filters = $this->getFilters();
@endphp

<x-filament-widgets::widget class="filament-widgets-chart-widget">
    <x-filament::card>
        @if ($heading || $filters)
            <div class="flex items-center justify-between gap-8">
                @if ($heading)
                    <x-filament::card.heading>
                        {{ $heading }}
                    </x-filament::card.heading>
                @endif

                @if ($filters)
                    <select
                        class="text-gray-900 border-gray-300 block h-10 transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:border-primary-500"
                        wire:model="filter"
                        wire:loading.class="animate-pulse"
                    >
                        @foreach ($filters as $value => $label)
                            <option value="{{ $value }}">
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
        @endif

        <div @if ($pollingInterval = $this->getPollingInterval()) wire:poll.{{ $pollingInterval }}="updateChartData" @endif>
            <div
                x-ignore
                ax-load
                ax-load-src="/js/filament/widgets/components/chart.js?v={{ \Composer\InstalledVersions::getVersion('filament/support') }}"
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
