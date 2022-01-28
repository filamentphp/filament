<x-filament::page class="filament-dashboard-page">
    @if ($widgets = \Filament\Facades\Filament::getWidgets())
        <x-filament::widgets :widgets="$widgets" />
    @endif
</x-filament::page>
