<x-filament::page>
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-8">
        @foreach (\Filament\Facades\Filament::getWidgets() as $widget)
            @livewire(\Livewire\Livewire::getAlias($widget))
        @endforeach
    </div>
</x-filament::page>
