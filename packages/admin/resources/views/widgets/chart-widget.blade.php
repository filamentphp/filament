<x-filament::card>

    <x-filament::card.filter :filter="$filter" />

    <div class="bg-gray-900" {!! ($pollingInterval = $this->getPollingInterval()) ? "wire:poll.{$pollingInterval}=\"updateChartData\"" : '' !!}>
        <canvas x-data="{
                chart: null,
                renderChart: function (data = null) {
                    this.chart = new Chart(
                        $el,
                        {
                            type: '{{ $this->getType() }}',
                            data: data != null ? data : {{ json_encode($this->getData()) }} ,
                            options: {{ json_encode($this->getOptions()) ?? '{}' }},
                        }
                    )
                },

                init: function () {
                    this.renderChart()

                    $wire.on('updateChartData', async ({ data }) => {
                        this.chart.destroy()
                        this.renderChart(data)
                    })
                },
            }" wire:ignore></canvas>
    </div>
</x-filament::card>
