@props([
    'default' => 1,
    'sm' => null,
    'md' => null,
    'lg' => null,
    'xl' => null,
    'twoXl' => null,
    'defaultStart' => null,
    'smStart' => null,
    'mdStart' => null,
    'lgStart' => null,
    'xlStart' => null,
    'twoXlStart' => null,
    'hidden' => false,
])

@php
    $getSpanValue = function ($span) {
        if ($span === 'full') {
            return '1 / -1';
        }

        return "span {$span} / span {$span}";
    }
@endphp

<div
    style="
        @if ($default) --col-span-default: {{ $getSpanValue($default) }}; @endif
        @if ($sm) --col-span-sm: {{ $getSpanValue($sm) }}; @endif
        @if ($md) --col-span-md: {{ $getSpanValue($md) }}; @endif
        @if ($lg) --col-span-lg: {{ $getSpanValue($lg) }}; @endif
        @if ($xl) --col-span-xl: {{ $getSpanValue($xl) }}; @endif
        @if ($twoXl) --col-span-2xl: {{ $getSpanValue($twoXl) }}; @endif
        @if ($defaultStart) --col-start-default: {{ $defaultStart }}; @endif
        @if ($smStart) --col-start-sm: {{ $smStart }}; @endif
        @if ($mdStart) --col-start-md: {{ $mdStart }}; @endif
        @if ($lgStart) --col-start-lg: {{ $lgStart }}; @endif
        @if ($xlStart) --col-start-xl: {{ $xlStart }}; @endif
        @if ($twoXlStart) --col-start-2xl: {{ $twoXlStart }}; @endif
    "
    {{ $attributes->class([
        'hidden' => $hidden,
        'col-[var(--col-span-default)]' => $default && (! $hidden),
        'sm:col-[var(--col-span-sm)]' => $sm && (! $hidden),
        'md:col-[var(--col-span-md)]' => $md && (! $hidden),
        'lg:col-[var(--col-span-lg)]' => $lg && (! $hidden),
        'xl:col-[var(--col-span-xl)]' => $xl && (! $hidden),
        '2xl:col-[var(--col-span-2xl)]' => $twoXl && (! $hidden),
        'col-start-[var(--col-start-default)]' => $defaultStart && (! $hidden),
        'sm:col-start-[var(--col-start-sm)]' => $smStart && (! $hidden),
        'md:col-start-[var(--col-start-md)]' => $mdStart && (! $hidden),
        'lg:col-start-[var(--col-start-lg)]' => $lgStart && (! $hidden),
        'xl:col-start-[var(--col-start-xl)]' => $xlStart && (! $hidden),
        '2xl:col-start-[var(--col-start-2xl)]' => $twoXlStart && (! $hidden),
    ]) }}
>
    {{ $slot }}
</div>
