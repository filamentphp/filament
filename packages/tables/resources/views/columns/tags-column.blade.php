@php
    $state = $getTags();
@endphp

<div {{ $attributes->merge($getExtraAttributes())->class([
    'px-4 py-3 flex flex-wrap gap-1 filament-tables-tags-column',
    match ($getAlignment()) {
        'left' => 'justify-start',
        'center' => 'justify-center',
        'right' => 'justify-end',
        default => null,
    },
]) }}>
    @foreach ($getTags() as $tag)
        <span @class([
            'inline-flex items-center justify-center min-h-6 px-2 py-0.5 text-sm font-medium tracking-tight rounded-xl text-primary-700 bg-primary-500/10 whitespace-normal',
            'dark:text-primary-500' => config('tables.dark_mode'),
        ])>
            {{ $tag }}
        </span>
    @endforeach
</div>
