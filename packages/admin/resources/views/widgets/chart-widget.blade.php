<x-filament::card>
    <x-slot name="heading">
        {{ $this->getHeading() }}
    </x-slot>

    <div {!! ($pollingInterval = $this->getPollingInterval()) ? "wire:poll.{$pollingInterval}=\"updateChartData\"" : '' !!}>
        <canvas
            x-data="{
                {{ $this->getChartVariableName() }}: null,

                init: function () {
                    {{ $this->getChartVariableName() }} = new Chart(
                        $el,
                        {
                            type: '{{ $this->getType() }}',
                            data: {{ json_encode($this->getData()) }},
                            options: {{ json_encode($this->getOptions()) ?? '{}' }},
                        },
                    )

                    $wire.on('updateChartData', async ({ data }) => {
                        {{ $this->getChartVariableName() }}.data = data

                        {{ $this->getChartVariableName() }}.update('resize')
                    })
                }
            }"
            wire:ignore
        ></canvas>
    </div>
</x-filament::card>
