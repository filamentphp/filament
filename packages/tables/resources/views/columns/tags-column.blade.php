@php
    $state = $getTags();
@endphp

<div {{ $attributes->merge($getExtraAttributes())->class([
    'filament-tables-tags-column px-4 py-3 flex flex-wrap items-center gap-1',
    match ($getAlignment()) {
        'left' => 'justify-start',
        'center' => 'justify-center',
        'right' => 'justify-end',
        default => null,
    },
]) }}>
    @foreach (array_slice($getTags(), 0, $getLimit()) as $tag)
        <span @class([
            'inline-flex items-center justify-center min-h-6 px-2 py-0.5 text-sm font-medium tracking-tight rounded-xl text-primary-700 bg-primary-500/10 whitespace-normal',
            'dark:text-primary-500' => config('tables.dark_mode'),
        ])>
            {{ $tag }}
        </span>
    @endforeach

    @if ($hasActiveLimit())
        <span class="text-xs ml-1">
            {{ trans_choice('tables::table.columns.tags.more', count($getTags()) - $getLimit()) }}
        </span>
    @endif
</div>
