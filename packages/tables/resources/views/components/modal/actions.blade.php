@props([
    'align' => 'left',
    'fullWidth' => false,
])

<div {{ $attributes->class([
    'flex flex-wrap items-center gap-4' => ! $fullWidth,
    'justify-end' => (! $fullWidth) && ($align === 'right'),
    'justify-center' => (! $fullWidth) && ($align === 'center'),
    'grid gap-2 grid-cols-[repeat(auto-fit,minmax(0,1fr))]' => $fullWidth,
]) }}>
    {{ $slot }}
</div>
