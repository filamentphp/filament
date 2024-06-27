@php
    $columns = $this->getColumns();
    $breakPoint = $this->getColumnBreakpoint();
@endphp

<x-filament-widgets::widget class="fi-wi-stats-overview">
    <div class="{{ ($columns < $breakPoint)
                ? 'fi-wi-stats-overview-stats-ctn grid gap-6 md:grid-cols-'.$columns
                : 'fi-wi-stats-overview-stats-ctn grid gap-6 md:grid-cols-2 xl:grid-cols-'.$columns
                }}">

        @if ($pollingInterval = $this->getPollingInterval())
            wire:poll.{{ $pollingInterval }}
        @endif
    >
        @foreach ($this->getCachedStats() as $stat)
            {{ $stat }}
        @endforeach
    </div>
</x-filament-widgets::widget>
