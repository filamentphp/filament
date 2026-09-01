@php
    use Filament\Support\View\ComponentAttributeBag;

    $columns = $this->getColumns();
    $pollingInterval = $this->getPollingInterval();

    $heading = $this->getHeading();
    $description = $this->getDescription();
    $hasHeading = filled($heading);
    $hasDescription = filled($description);
@endphp

<x-filament-widgets::widget
    :attributes="
        (new ComponentAttributeBag)
            ->merge([
                'wire:poll.' . $pollingInterval => $pollingInterval ? true : null,
            ], escape: false)
            ->class([
                'fi-wi-stats-overview',
            ])
    "
>
    {{ $this->content }}
</x-filament-widgets::widget>
