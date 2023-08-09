<x-filament-panels::page class="fi-dashboard-page">
    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :data="$this->getWidgetData()"
        :widgets="$this->getVisibleWidgets()"
    />
</x-filament-panels::page>
