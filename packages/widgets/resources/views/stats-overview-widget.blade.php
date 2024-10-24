@php
    $columns = $this->getColumns();
    $pollingInterval = $this->getPollingInterval();
@endphp

<x-filament-widgets::widget
    :attributes="
        (new \Illuminate\View\ComponentAttributeBag)
            ->merge([
                'wire:poll.' . $pollingInterval => $pollingInterval,
            ])
            ->class([
                'fi-wi-stats-overview',
                'fi-grid-cols-' . $columns,
            ])
    "
>
    @foreach ($this->getCachedStats() as $stat)
        {{ $stat }}
    @endforeach
</x-filament-widgets::widget>
