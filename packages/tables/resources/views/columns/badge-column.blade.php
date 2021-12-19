@php
    $state = $getFormattedState();

    $stateColor = match ($getStateColor()) {
        'danger' => 'text-danger-700 bg-danger-500/10',
        'primary' => 'text-primary-700 bg-primary-500/10',
        'success' => 'text-success-700 bg-success-500/10',
        'warning' => 'text-warning-700 bg-warning-500/10',
        default => 'text-gray-700 bg-gray-500/10',
    };
@endphp

<div {{ $attributes->merge($getExtraAttributes())->class(['px-4 py-3']) }}>
    @if (filled($state))
        <span @class([
            'inline-flex items-center justify-center h-6 px-2 text-sm font-medium tracking-tight rounded-full',
            $stateColor => $stateColor,
        ])>
            {{ $state }}
        </span>
    @endif
</div>
