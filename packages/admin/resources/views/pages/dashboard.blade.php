<x-filament::page>
    @if ($widgets = \Filament\Facades\Filament::getWidgets())
        <x-filament::widgets>
            @foreach ($widgets as $widget)
                @livewire(\Livewire\Livewire::getAlias($widget))
            @endforeach
        </x-filament::widgets>
    @endif
</x-filament::page>
