<x-filament::page class="fi-dashboard-page">
    <x-filament-widgets::widgets
        :widgets="$this->getVisibleWidgets()"
        :columns="$this->getColumns()"
    />
</x-filament::page>
