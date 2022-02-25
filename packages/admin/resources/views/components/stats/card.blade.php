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
    'filters' => null,
    'id' => null,
])

<div
    {{     $attributes->class(['relative p-6 rounded-2xl filament-stats-card', 'bg-white shadow' => !$flat, 'dark:bg-gray-800' => !$flat && config('filament.dark_mode'), 'border' => $flat, 'dark:border-gray-700' => $flat && config('filament.dark_mode')]) }}>
    <div @class(['space-y-2'])>
        <div @class([
            'flex flex-wrap justify-between items-center space-y-1' => $filters,
        ])>
            <div @class([
                'text-sm font-medium text-gray-500',
                'dark:text-gray-200' => config('filament.dark_mode'),
            ])>
                {{ $label }}
            </div>
            @if ($filters)
                <div x-id="['stats-widget-filter']">
                    <select :id="$id('stats-widget-filter')" :name="$id('stats-widget-filter')"
                        wire:model="filters.{{ $id }}"
                        class="text-sm font-medium text-gray-500 block transition duration-75 rounded-lg shadow-sm focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600 disabled:opacity-70 filament-forms-select-component dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600"
                        style="padding-block: 4px">
                        @foreach ($filters as $val => $title)
                            <option value="{{ $val }}">
                                {{ $title }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
        <div class="text-3xl">
            {{ $value }}
        </div>

        @if ($description)
            <div @class([
                'flex items-center space-x-1 rtl:space-x-reverse text-sm font-medium',
                match ($descriptionColor) {
                    'danger' => 'text-danger-600',
                    'primary' => 'text-primary-600',
                    'success' => 'text-success-600',
                    'warning' => 'text-warning-600',
                    default => 'text-gray-600'
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
            <canvas x-data="{
                    chart: null,

                    init: function () {
                        let chart = this.initChart()

                        window.addEventListener('updateStatsChartData', (event) => {
                            if (@js($id) === event.detail.id) {
                                chart.destroy()
                                chart = this.initChart(event.detail.data)
                            }
                        })
                    },

                    initChart: function (data = null) {
                        data = data ?? @js($chart)

                        return this.chart = new Chart($el, {
                            type: 'line',
                            data: {
                                labels: @js($chart),
                                datasets: [{
                                    data: data,
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
                        })
                    }
                }" wire:ignore class="h-6">
                <span x-ref="backgroundColorElement" @class([
                    match ($chartColor) {
                        'danger' => \Illuminate\Support\Arr::toCssClasses([
                            'text-danger-50',
                            'dark:text-danger-700' => config('filament.dark_mode'),
                        ]),
                        'primary' => \Illuminate\Support\Arr::toCssClasses([
                            'text-primary-50',
                            'dark:text-primary-700' => config('filament.dark_mode'),
                        ]),
                        'success' => \Illuminate\Support\Arr::toCssClasses([
                            'text-success-50',
                            'dark:text-success-700' => config('filament.dark_mode'),
                        ]),
                        'warning' => \Illuminate\Support\Arr::toCssClasses([
                            'text-warning-50',
                            'dark:text-warning-700' => config('filament.dark_mode'),
                        ]),
                        default => \Illuminate\Support\Arr::toCssClasses([
                            'text-gray-50',
                            'dark:text-gray-700' => config('filament.dark_mode'),
                        ])
                    },
                ])></span>

                <span x-ref="borderColorElement" @class([
                    match ($chartColor) {
                        'danger' => 'text-danger-400',
                        'primary' => 'text-primary-400',
                        'success' => 'text-success-400',
                        'warning' => 'text-warning-400',
                        default => 'text-gray-400'
                    },
                ])></span>
            </canvas>
        </div>
    @endif
</div>
