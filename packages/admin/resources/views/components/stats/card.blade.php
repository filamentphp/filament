@props([
    'chart' => null,
    'chartColor' => null,
    'color' => null,
    'description' => null,
    'descriptionColor' => null,
    'descriptionIcon' => null,
    'flat' => false,
    'label' => null,
    'value' => null,
])

<div {{ $attributes->class([
    'relative p-6 rounded-2xl',
    'bg-white shadow' => ! $flat,
    'border' => $flat,
    'filament-stats-card'
]) }}>
    <div @class([
        'space-y-2',
    ])>
        <div class="text-sm font-medium text-gray-500">
            {{ $label }}
        </div>

        <div class="text-3xl">
            {{ $value }}
        </div>

        @if ($description)
            <div @class([
                'flex items-center space-x-1 text-sm font-medium',
                match ($descriptionColor) {
                    'danger' => 'text-danger-600',
                    'primary' => 'text-primary-600',
                    'success' => 'text-success-600',
                    'warning' => 'text-warning-600',
                    default => 'text-gray-600',
                },
            ])>
                <span>{{ $description }}</span>

                @if ($descriptionIcon)
                    <x-dynamic-component :component="$descriptionIcon" class="w-4 h-4" />
                @endif
            </div>
        @endif
    </div>

    @if ($chart)
        <div class="absolute bottom-0 inset-x-0 rounded-b-2xl overflow-hidden">
            <canvas
                x-data="{
                    chart: null,

                    init: function () {
                        chart = new Chart(
                            $el,
                            {
                                type: 'line',
                                data: {
                                    labels: {{ json_encode(array_keys($chart)) }},
                                    datasets: [{
                                        data: {{ json_encode(array_values($chart)) }},
                                        backgroundColor: getComputedStyle($refs.backgroundColorElement).color,
                                        borderColor: getComputedStyle($refs.borderColorElement).color,
                                        borderWidth: 2,
                                        fill: 'start',
                                        tension: 0.5,
                                    }],
                                },
                                options: {
                                    elements: {
                                        point: {
                                            radius: 0,
                                        },
                                    },
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false,
                                        },
                                    },
                                    scales: {
                                        x:  {
                                            display: false,
                                        },
                                        y:  {
                                            display: false,
                                        },
                                    },
                                    tooltips: {
                                        enabled: false,
                                    },
                                },
                            }
                        )
                    },
                }"
                wire:ignore
                class="h-6"
            >
                <span
                    x-ref="backgroundColorElement"
                    @class([
                        match ($chartColor) {
                            'danger' => 'text-danger-50',
                            'primary' => 'text-primary-50',
                            'success' => 'text-success-50',
                            'warning' => 'text-warning-50',
                            default => 'text-gray-50',
                        },
                    ])
                ></span>

                <span
                    x-ref="borderColorElement"
                    @class([
                        match ($chartColor) {
                            'danger' => 'text-danger-400',
                            'primary' => 'text-primary-400',
                            'success' => 'text-success-400',
                            'warning' => 'text-warning-400',
                            default => 'text-gray-400',
                        },
                    ])
                ></span>
            </canvas>
        </div>
    @endif
</div>
