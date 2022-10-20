<x-filament-widgets::widget class="filament-stats-overview-widget">
    <div {!! ($pollingInterval = $this->getPollingInterval()) ? "wire:poll.{$pollingInterval}" : '' !!}>
        <x-filament-widgets::stats :columns="$this->getColumns()">
            @foreach ($this->getCachedCards() as $card)
                {{ $card }}
            @endforeach
        </x-filament-widgets::stats>
    </div>
</x-filament-widgets::widget>
