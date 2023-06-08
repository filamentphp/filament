<x-filament-widgets::widget class="filament-stats-overview-widget">
    <div
        @if ($pollingInterval = $this->getPollingInterval()) wire:poll.{{ $pollingInterval }} @endif
    >
        @php
            $columns = $this->getColumns();
        @endphp

        <div
            @class([
                'filament-stats grid gap-4 lg:gap-8',
                'md:grid-cols-3' => $columns === 3,
                'md:grid-cols-1' => $columns === 1,
                'md:grid-cols-2' => $columns === 2,
                'md:grid-cols-2 xl:grid-cols-4' => $columns === 4,
            ])
        >
            @foreach ($this->getCachedCards() as $card)
                {{ $card }}
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
