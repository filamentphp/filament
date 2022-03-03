<x-filament::widget class="filament-stats-overview-widget">
    <div {!! ($pollingInterval = $this->getPollingInterval()) ? "wire:poll.{$pollingInterval}" : '' !!}>
        <x-filament::stats :columns="$this->getColumns()">
            @foreach ($this->getCachedCards() as $card)
                {{ $card }}
            @endforeach
        </x-filament::stats>
    </div>
</x-filament::widget>
