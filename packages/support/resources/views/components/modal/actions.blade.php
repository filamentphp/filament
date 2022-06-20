@props([
    'align' => 'left',
    'darkMode' => false,
    'fullWidth' => false,
])

<div {{ $attributes->class([
    'filament-modal-actions',
    'flex flex-wrap items-center gap-4 rtl:space-x-reverse' => ! $fullWidth,
    'flex-row-reverse space-x-reverse' => (! $fullWidth) && ($align === 'right'),
    'justify-center' => (! $fullWidth) && ($align === 'center'),
    'grid gap-2 grid-cols-[repeat(auto-fit,minmax(0,1fr))]' => $fullWidth,
]) }}>
    {{ $slot }}
</div>
