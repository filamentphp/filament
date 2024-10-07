<x-filament-panels::page class="fi-dashboard-page">
    @if (method_exists($this, 'getFiltersForm'))
        {{ $this->getFiltersForm() }}
    @endif

    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :data="
            [
                ...(property_exists($this, 'filters') ? ['pageFilters' => $this->filters] : []),
                ...$this->getWidgetData(),
            ]
        "
        :widgets="$this->getVisibleWidgets()"
    />
</x-filament-panels::page>
