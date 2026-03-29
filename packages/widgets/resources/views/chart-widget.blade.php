@php
    use Filament\Widgets\View\Components\ChartWidgetComponent;
    use Illuminate\View\ComponentAttributeBag;

    $color = $this->getColor();
    $heading = $this->getHeading();
    $description = $this->getDescription();
    $filters = $this->getFilters();
    $isCollapsible = $this->isCollapsible();
    $type = $this->getType();
    $isEmpty = $this->isEmpty();
@endphp

<x-filament-widgets::widget class="fi-wi-chart">
    <x-filament::section
        :description="$description"
        :heading="$heading"
        :collapsible="$isCollapsible"
    >
        @if ($filters || method_exists($this, 'getFiltersSchema'))
            <x-slot name="afterHeader">
                @if ($filters)
                    <x-filament::input.wrapper
                        inline-prefix
                        wire:target="filter"
                        class="fi-wi-chart-filter"
                    >
                        <x-filament::input.select
                            inline-prefix
                            wire:model.live="filter"
                        >
                            @foreach ($filters as $value => $label)
                                <option value="{{ $value }}">
                                    {{ $label }}
                                </option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                @endif

                @if (method_exists($this, 'getFiltersSchema'))
                    <x-filament::dropdown
                        placement="bottom-end"
                        shift
                        width="xs"
                        class="fi-wi-chart-filter"
                    >
                        <x-slot name="trigger">
                            {{ $this->getFiltersTriggerAction() }}
                        </x-slot>

                        <div class="fi-wi-chart-filter-content">
                            {{ $this->getFiltersSchema() }}

                            @if (method_exists($this, 'hasDeferredFilters') && $this->hasDeferredFilters())
                                <div
                                    class="fi-wi-chart-filter-content-actions-ctn"
                                >
                                    {{ $this->getFiltersApplyAction() }}

                                    {{ $this->getFiltersResetAction() }}
                                </div>
                            @endif
                        </div>
                    </x-filament::dropdown>
                @endif
            </x-slot>
        @endif

        <div
            @if ($pollingInterval = $this->getPollingInterval())
                wire:poll.{{ $pollingInterval }}="updateChartData"
            @endif
        >
            @if ($isEmpty)
                @if ($emptyState = $this->getEmptyState())
                    {{ $emptyState }}
                @else
                    <div class="fi-wi-chart-empty-state">
                        <div class="fi-wi-chart-empty-state-content">
                            <div class="fi-wi-chart-empty-state-icon-bg">
                                {{ \Filament\Support\generate_icon_html($this->getEmptyStateIcon(), size: \Filament\Support\Enums\IconSize::Large) }}
                            </div>

                            <h2 class="fi-wi-chart-empty-state-heading">
                                {{ $this->getEmptyStateHeading() }}
                            <h2>

                            @if (filled($emptyStateDescription = $this->getEmptyStateDescription()))
                                <p class="fi-wi-chart-empty-state-description">
                                    {{ $emptyStateDescription }}
                                </p>
                            @endif

                            @if ($emptyStateActions = array_filter(
                                $this->getEmptyStateActions(),
                                fn (\Filament\Actions\Action | \Filament\Actions\ActionGroup $action): bool => $action->isVisible()
                            ))
                                <div class="fi-wi-chart-actions fi-align-center fi-wrapped">
                                    @foreach ($emptyStateActions as $action)
                                        {{ $action }}
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <div
                    x-load
                    x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('chart', 'filament/widgets') }}"
                    wire:ignore
                    data-chart-type="{{ $type }}"
                    x-data="chart({
                                cachedData: @js($this->getCachedData()),
                                maxHeight: @js($maxHeight = $this->getMaxHeight()),
                                options: @js($this->getOptions()),
                                type: @js($type),
                            })"
                    {{
                        (new ComponentAttributeBag)
                            ->color(ChartWidgetComponent::class, $color)
                            ->class([
                                'fi-wi-chart-canvas-ctn',
                                'fi-wi-chart-canvas-ctn-no-aspect-ratio' => filled($maxHeight),
                            ])
                    }}
                >
                    <canvas
                        x-ref="canvas"
                        @if ($maxHeight)
                            style="max-height: {{ $maxHeight }}"
                        @endif
                    ></canvas>

                    <span
                        x-ref="backgroundColorElement"
                        class="fi-wi-chart-bg-color"
                    ></span>

                    <span
                        x-ref="borderColorElement"
                        class="fi-wi-chart-border-color"
                    ></span>

                    <span
                        x-ref="gridColorElement"
                        class="fi-wi-chart-grid-color"
                    ></span>

                    <span
                        x-ref="textColorElement"
                        class="fi-wi-chart-text-color"
                    ></span>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
