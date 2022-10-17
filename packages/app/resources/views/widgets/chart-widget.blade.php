@php
    $heading = $this->getHeading();
    $filters = $this->getFilters();
@endphp

<x-filament::widget class="filament-widgets-chart-widget">
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
                        wire:model="filter"
                        @class([
                            'text-gray-900 border-gray-300 block h-10 transition duration-75 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500',
                            'dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:focus:border-primary-500' => config('filament.dark_mode'),
                        ])
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

            <x-filament::hr />
        @endif

        <div {!! ($pollingInterval = $this->getPollingInterval()) ? "wire:poll.{$pollingInterval}=\"updateChartData\"" : '' !!}>
            <canvas
                x-data="{
                    chart: null,

                    init: function () {
                        let chart = this.initChart()

                        $wire.on('updateChartData', async ({ data }) => {
                            chart.data = this.applyColorToData(data)
                            chart.update('resize')
                        })

                        $wire.on('filterChartData', async ({ data }) => {
                            chart.destroy()
                            chart = this.initChart(data)
                        })
                    },

                    initChart: function (data = null) {
                        data = data ?? {{ json_encode($this->getCachedData()) }}

                        return this.chart = new Chart($el, {
                            type: '{{ $this->getType() }}',
                            data: this.applyColorToData(data),
                            options: {{ json_encode($this->getOptions()) }} ?? {},
                        })
                    },

                    applyColorToData: function (data) {
                        data.datasets.forEach((dataset, datasetIndex) => {
                            if (! dataset.backgroundColor) {
                                data.datasets[datasetIndex].backgroundColor = getComputedStyle($refs.backgroundColorElement).color
                            }

                            if (! dataset.borderColor) {
                                data.datasets[datasetIndex].borderColor = getComputedStyle($refs.borderColorElement).color
                            }
                        })

                        return data
                    },
                }"
                wire:ignore
                @if ($maxHeight = $this->getMaxHeight())
                    style="max-height: {{ $maxHeight }}"
                @endif
            >
                <span
                    x-ref="backgroundColorElement"
                    @class([
                        'text-gray-50',
                        'dark:text-gray-300' => config('filament.dark_mode'),
                    ])
                ></span>

                <span
                    x-ref="borderColorElement"
                    @class([
                        'text-gray-500',
                        'dark:text-gray-200' => config('filament.dark_mode'),
                    ])
                ></span>
            </canvas>
        </div>
    </x-filament::card>
</x-filament::widget>
