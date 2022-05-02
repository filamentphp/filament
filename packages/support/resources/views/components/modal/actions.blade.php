@props([
    'align' => 'left',
    'fullWidth' => false,
])

<div {{ $attributes->class([
    'filament-modal-actions',
    'flex items-center space-x-4 rtl:space-x-reverse' => ! $fullWidth,
    'justify-end' => (! $fullWidth) && ($align === 'right'),
    'justify-center' => (! $fullWidth) && ($align === 'center'),
    'grid gap-2 grid-cols-[repeat(auto-fit,minmax(0,1fr))]' => $fullWidth,
]) }}>
    {{ $slot }}
</div>
