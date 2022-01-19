<x-filament::page class="filament-pages-dashboard">
    @if ($widgets = \Filament\Facades\Filament::getWidgets())
        <x-filament::widgets :widgets="$widgets" />
    @endif
</x-filament::page>
