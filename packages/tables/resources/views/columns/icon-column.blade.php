@php
    $stateColor = match ($getStateColor()) {
        'danger' => 'text-danger-500',
        'primary' => 'text-primary-500',
        'success' => 'text-success-500',
        'warning' => 'text-warning-500',
        null => \Illuminate\Support\Arr::toCssClasses(['text-gray-700', 'dark:text-gray-200' => config('tables.dark_mode'),]),
        default => $getStateColor(),
    };
@endphp

<div
    {{ $attributes->merge($getExtraAttributes())->class([
        'px-4 py-3 filament-tables-icon-column',
        'flex flex-row gap-x-2'
    ]) }}
>
    @if($prefix = $getPrefix())
        <span class="whitespace-nowrap group-focus-within:text-primary-500">
            {{ $prefix }}
        </span>
    @endif

    @if ($icon = $getStateIcon())
        <x-dynamic-component
            :component="$getStateIcon()"
            :class="'w-6 h-6 ' . $stateColor"
        />
    @endif

    @if ($suffix = $getSuffix())
        <span class="whitespace-nowrap group-focus-within:text-primary-500">
        {{ $suffix }}
        </span>
    @endif
</div>
