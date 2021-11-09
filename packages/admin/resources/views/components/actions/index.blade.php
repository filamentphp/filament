@props([
    'actions',
])

@php
    $actions = array_filter(
        $actions,
        fn (\Filament\View\Components\Actions\Action $action): bool => ! $action->isHidden(),
    );
@endphp

@if (count($actions))
    <div {{ $attributes->class('flex items-center gap-4 -my-2') }}>
        @foreach ($actions as $action)
            {{ $action }}
        @endforeach
    </div>
@endif
