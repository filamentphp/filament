<x-filament::widget class="filament-stats-overview-widget">
    <x-filament::stats :columns="$this->getColumns()">
        @foreach ($this->getCards() as $card)
            {{ $card }}
        @endforeach
    </x-filament::stats>
</x-filament::widget>
