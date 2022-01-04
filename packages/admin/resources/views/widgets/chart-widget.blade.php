<x-filament::card>
    <div class="flex items-center justify-between gap-8">
        <x-filament::card.heading>
            {{ $this->getHeading() }}
        </x-filament::card.heading>

        @if ($filters = $this->getFilters())
            <select
                wire:model="filter"
                class="text-gray-900 border-gray-300 block h-10 transition duration-75 rounded-lg shadow-sm focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600"
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
                renderChart: function (data = null) {
                    this.chart = new Chart($el,{
                        type: '{{ $this->getType() }}',
                        data: data != null ? data : {{ json_encode($this->getData()) }},
                        options: {{ json_encode($this->getOptions()) ?? '{}' }},
                    })
                },
                init: function () {
                    if ({!! count($filters) !!}) {
                        this.renderChart()
                    } else {
                        chart = new Chart($el,{
                            type: '{{ $this->getType() }}',
                            data: {{ json_encode($this->getData()) }},
                            options: {{ json_encode($this->getOptions()) ?? '{}' }},
                        })
                    }

                    $wire.on('updateChartData', async ({ data }) => {
                        chart.data = data
                        chart.update('resize')
                    })

                    $wire.on('filteredChartData', async ({ data }) => {
                        this.chart.destroy()
                        this.renderChart(data)
                    })
                },
            }"
            wire:ignore
        ></canvas>
    </div>
</x-filament::card>
