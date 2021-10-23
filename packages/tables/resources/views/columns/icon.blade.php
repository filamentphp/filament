@php
    $stateColor = $getStateColor() ?? 'secondary';

    $stateColor = [
        'danger' => 'text-danger-500',
        'primary' => 'text-primary-500',
        'secondary' => 'text-gray-700',
        'success' => 'text-success-500',
        'warning' => 'text-warning-500',
    ][$stateColor] ?? $stateColor;
@endphp

<div class="px-4 py-3">
    @if ($getStateIcon())
        <x-dynamic-component
            :component="$getStateIcon()"
            :class="'w-6 h-6' . ($stateColor ? ' ' . $stateColor : '')"
        />
    @endif
</div>
