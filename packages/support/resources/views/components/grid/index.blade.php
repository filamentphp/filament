@props([
    'isGrid' => true,
    'default' => 1,
    'direction' => 'row',
    'sm' => null,
    'md' => null,
    'lg' => null,
    'xl' => null,
    'twoXl' => null,
])

<div
    style="
        @if ($default) --cols-default: repeat({{ $default }}, minmax(0, 1fr)); @endif
        @if ($sm) --cols-sm: repeat({{ $sm }}, minmax(0, 1fr)); @endif
        @if ($md) --cols-md: repeat({{ $md }}, minmax(0, 1fr)); @endif
        @if ($lg) --cols-lg: repeat({{ $lg }}, minmax(0, 1fr)); @endif
        @if ($xl) --cols-xl: repeat({{ $xl }}, minmax(0, 1fr)); @endif
        @if ($twoXl) --cols-2xl: repeat({{ $twoXl }}, minmax(0, 1fr)); @endif
    "
    {{ $attributes->class([
        'grid' => $isGrid && $direction === 'row',
        'grid-cols-[var(--cols-default)]' => $default && ($direction === 'row'),
        'columns-[var(--cols-default)]' => $default && ($direction === 'column'),
        'sm:grid-cols-[var(--cols-sm)]' => $sm && ($direction === 'row'),
        'sm:columns-[var(--cols-sm)]' => $sm && ($direction === 'column'),
        'md:grid-cols-[var(--cols-md)]' => $md && ($direction === 'row'),
        'md:columns-[var(--cols-md)]' => $md && ($direction === 'column'),
        'lg:grid-cols-[var(--cols-lg)]' => $lg && ($direction === 'row'),
        'lg:columns-[var(--cols-lg)]' => $lg && ($direction === 'column'),
        'xl:grid-cols-[var(--cols-xl)]' => $xl && ($direction === 'row'),
        'xl:columns-[var(--cols-xl)]' => $xl && ($direction === 'column'),
        '2xl:grid-cols-[var(--cols-2xl)]' => $twoXl && ($direction === 'row'),
        '2xl:columns-[var(--cols-2xl)]' => $twoXl && ($direction === 'column'),
    ]) }}
>
    {{ $slot }}
</div>
