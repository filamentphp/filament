@php
    $state = $getState();

    $stateIcon = $getStateIcon() ?? ($state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle');
    $stateColor = $getStateColor() ?? ($state ? 'success' : 'danger');

    $stateColor = match ($stateColor) {
        'danger' => 'text-danger-500',
        'primary' => 'text-primary-500',
        'success' => 'text-success-500',
        'warning' => 'text-warning-500',
        default => 'text-gray-700',
    };

    $affixLabelClasses = [
        'whitespace-nowrap group-focus-within:text-primary-500',
    ];
@endphp

<div
    {{ $attributes->merge($getExtraAttributes())->class([
        'px-4 py-3 filament-tables-boolean-column',
        'flex flex-row gap-x-2'
    ]) }}
>
    @if ($prefix = $getPrefix())
        <span class="whitespace-nowrap group-focus-within:text-primary-500">
            {{ $prefix }}
        </span>
    @endif

    @if ($state !== null)
        <x-dynamic-component
            :component="$stateIcon"
            :class="'w-6 h-6' . ' ' . $stateColor"
        />
    @endif

    @if ($suffix = $getSuffix())
        <span class="whitespace-nowrap group-focus-within:text-primary-500">
            {{ $suffix }}
        </span>
    @endif
</div>
