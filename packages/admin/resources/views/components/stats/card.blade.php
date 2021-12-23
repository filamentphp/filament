@props([
    'description' => null,
    'descriptionColor' => null,
    'descriptionIcon' => null,
    'flat' => false,
    'label' => null,
    'value' => null,
    'chart' => null,
])

<div
    {{     $attributes->class(['relative p-6 rounded-2xl', 'bg-white shadow' => !$flat, 'border' => $flat]) }}>
    <div class="space-y-2 {{ blank($chart) ?: 'mb-4' }}">
        <div class="text-sm font-medium text-gray-500">{{ $label }}</div>

        <div class="text-3xl">{{ $value }}</div>

        @if ($description)
            <div
                class="flex items-center space-x-1 text-sm font-medium {{ [
                    null => 'text-gray-600',
                    'danger' => 'text-danger-600',
                    'primary' => 'text-primary-600',
                    'success' => 'text-success-600',
                    'warning' => 'text-warning-600',
                ][$descriptionColor] }}">
                <span>{{ $description }}</span>

                @if ($descriptionIcon)
                    <x-dynamic-component :component="$descriptionIcon" class="w-4 h-4" />
                @endif
            </div>
        @endif
    </div>

    @if ($chart)
        <div class="absolute bottom-0 inset-x-0 rounded-2xl overflow-hidden mt-6">
            <canvas height="50" x-data="{
                    chart: null,
                    chartOptions: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display:false
                            }
                        },
                        tooltips: {
                            enabled: false,
                        },
                        elements: {
                            point: {
                                radius: 0
                            },
                        },
                        scales: {
                            x:  {
                                display: false
                            },
                            y:  {
                                display: false
                            }
                        }
                    },
                    init: function () {
                        chart = new Chart(
                            $el,
                            {
                                type: 'line',
                                data: {{ json_encode($chart) }} ,
                                options: this.chartOptions,
                            }
                        )
                    },
                }" wire:ignore>
            </canvas>
        </div>
    @endif

</div>
