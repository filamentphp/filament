<x-filament::page class="filament-dashboard-page">
    <x-filament-widgets::widgets
        :widgets="$this->getWidgets()"
        :columns="$this->getColumns()"
    />
</x-filament::page>
