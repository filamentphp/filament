<x-filament::page class="fi-dashboard-page">
    <x-filament-widgets::widgets
        :widgets="$this->getWidgets()"
        :columns="$this->getColumns()"
    />
</x-filament::page>
