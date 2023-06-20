@php
    $descriptionIcon = $getDescriptionIcon();
    $descriptionIconPosition = $getDescriptionIconPosition();
    $url = $getUrl();
    $tag = $url ? 'a' : 'div';
@endphp

<{!! $tag !!}
    @if ($url)
        href="{{ $url }}"
        @if ($shouldOpenUrlInNewTab()) target="_blank" @endif
    @endif
    {{ $getExtraAttributeBag()->class(['filament-stats-overview-widget-card relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/20']) }}
>
    <div @class([
        'space-y-2',
    ])>
        <div
            class="flex items-center space-x-2 text-sm font-medium rtl:space-x-reverse"
        >
            @if ($icon = $getIcon())
                <x-filament::icon
                    :name="$icon"
                    alias="widgets::stats-overview.card"
                    color="text-gray-500 dark:text-gray-200"
                    size="h-4 w-4"
                />
            @endif

            <span>{{ $getLabel() }}</span>
        </div>

        <div class="text-3xl">
            {{ $getValue() }}
        </div>

        @if ($description = $getDescription())
            <div
                class="flex items-center space-x-1 text-sm font-medium text-custom-600 rtl:space-x-reverse"
                style="{{ \Filament\Support\get_color_css_variables($getDescriptionColor() ?? 'gray', shades: [600]) }}"
            >
                @if ($descriptionIcon && ($descriptionIconPosition === 'before'))
                    <x-filament::icon
                        :name="$descriptionIcon"
                        alias="widgets::stats-overview.card.description"
                        size="h-4 w-4"
                    />
                @endif

                <span>{{ $description }}</span>

                @if ($descriptionIcon && ($descriptionIconPosition === 'after'))
                    <x-filament::icon
                        :name="$descriptionIcon"
                        alias="widgets::stats-overview.card.description"
                        size="h-4 w-4"
                    />
                @endif
            </div>
        @endif
    </div>

    @if ($chart = $getChart())
        <div
            x-ignore
            ax-load
            ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('stats-overview/card/chart', 'filament/widgets') }}"
            x-data="statsOverviewCardChart({
                        labels: @js(array_keys($chart)),
                        values: @js(array_values($chart)),
                    })"
            wire:ignore
            x-on:theme-changed.window="
                chart.destroy()
                initChart()
            "
            class="absolute inset-x-0 bottom-0 overflow-hidden rounded-b-xl"
            style="{{ \Filament\Support\get_color_css_variables($getChartColor() ?? 'gray', shades: [50, 400, 700]) }}"
        >
            <canvas x-ref="canvas" class="h-6"></canvas>

            <span
                x-ref="backgroundColorElement"
                class="text-custom-50 dark:text-custom-700"
            ></span>

            <span x-ref="borderColorElement" class="text-custom-400"></span>
        </div>
    @endif
</{!! $tag !!}>
