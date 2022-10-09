@php
    $stateColor = match ($getStateColor()) {
        'danger' => 'text-danger-500',
        'primary' => 'text-primary-500',
        'success' => 'text-success-500',
        'warning' => 'text-warning-500',
        null => \Illuminate\Support\Arr::toCssClasses(['text-gray-700', 'dark:text-gray-200' => config('tables.dark_mode'),]),
        default => $getStateColor(),
    };

    $size = match ($getSize()) {
        'xs' => 'h-3 w-3'
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
        'xl' => 'h-7 w-7',
        default => 'w-6 h-6',
    };
@endphp

<div {{ $attributes->merge($getExtraAttributes())->class([
    'filament-tables-icon-column',
    'px-4 py-3' => ! $isInline(),
]) }}>
    @if ($getStateIcon())
        <x-dynamic-component
            :component="$getStateIcon()"
            :class="$size . ' ' . $stateColor"
        />
    @endif
</div>
