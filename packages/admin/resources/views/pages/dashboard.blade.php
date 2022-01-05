<x-filament::page>
    @if ($widgets = \Filament\Facades\Filament::getWidgets())
        <x-filament::widgets>
            @foreach ($widgets as $widget)
                @if ($widget::canView())
                    @livewire(\Livewire\Livewire::getAlias($widget))
                @endif
            @endforeach
        </x-filament::widgets>
    @endif
</x-filament::page>
