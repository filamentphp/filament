@php
    $url = $getUrl();
@endphp

<x-filament::stats.card
    :tag="$url ? 'a' : 'div'"
    :chart="$getChart()"
    :chart-color="$getChartColor()"
    :color="$getColor()"
    :icon="$getIcon()"
    :description="$getDescription()"
    :description-color="$getDescriptionColor()"
    :description-icon="$getDescriptionIcon()"
    :href="$url"
    :target="$shouldOpenUrlInNewTab() ? '_blank' : null"
    :label="$getLabel()"
    :value="$getValue()"
    class="filament-stats-overview-widget-card"
/>
