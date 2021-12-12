<x-filament::card>
    <x-slot name="heading">
        {{ $this->getHeading() }}
    </x-slot>

    <div {!! ($pollingInterval = $this->getPollingInterval()) ? "wire:poll.{$pollingInterval}=\"updateChartData\"" : '' !!}>
        <canvas
            x-data="{
                chart: null,

                init: function () {
                    chart = new Chart(
                        $el,
                        {
                            type: '{{ $this->getType() }}',
                            data: {{ json_encode($this->getData()) }},
                            options: {{ json_encode($this->getOptions()) ?? '{}' }},
                        },
                    )

                    $wire.on('updateChartData', async ({ data }) => {
                        chart.data = data

                        chart.update('resize')
                    })
                }
            }"
            wire:ignore
        ></canvas>
    </div>
</x-filament::card>
