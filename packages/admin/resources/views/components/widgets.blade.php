@props([
    'data' => [],
    'widgets' => [],
])

<div {{ $attributes->class(['grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-8 mb-6 filament-widgets-container']) }}>
    @foreach ($widgets as $widget)
        @if ($widget::canView())
            @livewire(\Livewire\Livewire::getAlias($widget), $data, key($widget))
        @endif
    @endforeach
</div>
