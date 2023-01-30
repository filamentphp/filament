@php
    $heading = $this->getHeading();
    $subheading = $this->getSubheading();
    $filters = $this->getFilters();
@endphp

<x-filament-widgets::widget class="filament-widgets-chart-widget">
    <x-filament::card>
        @if ($heading || $filters)
            <div class="flex items-center justify-between gap-8">
                <x-filament::card.header>
                    @if ($heading)
                        <x-filament::card.heading>
                            {{ $heading }}
                        </x-filament::card.heading>
                    @endif

                    @if ($subheading)
                        <x-filament::card.subheading>
                            {{ $subheading }}
                        </x-filament::card.subheading>
                    @endif
                </x-filament::card.header>

                @if ($filters)
                    <div class="flex items-center space-x-3">
                        @if ($showFilterLoadingIndicator)
                            <svg
                                wire:loading
                                wire:target="filter"
                                class="animate-spin h-8 w-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        @endif
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
                    </div>
                @endif
            </div>
        @endif

        <div @if ($pollingInterval = $this->getPollingInterval()) wire:poll.{{ $pollingInterval }}="updateChartData" @endif>
            <div
                x-ignore
                ax-load
                ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
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
