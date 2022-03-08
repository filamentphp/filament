<x-filament::widget class="filament-widgets-chart-widget">
    <x-filament::card>
        <div class="flex items-center justify-between gap-8">
            <x-filament::card.heading>
                {{ $this->getHeading() }}
            </x-filament::card.heading>

            @if ($filters = $this->getFilters())
                <select
                    wire:model="filter"
                    @class([
                        'text-gray-900 border-gray-300 block h-10 transition duration-75 rounded-lg shadow-sm focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600',
                        'dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200' => config('filament.dark_mode'),
                    ])
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
