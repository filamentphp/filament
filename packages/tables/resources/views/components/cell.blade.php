@props([
    'action' => null,
    'alignment' => null,
    'name',
    'record',
    'shouldOpenUrlInNewTab' => false,
    'url' => null,
])

<td {{ $attributes->class([
    'filament-tables-cell',
    'dark:text-white' => config('tables.dark_mode'),
    match ($alignment) {
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
        default => null,
    },
]) }}>
    @if ($action)
        <button
            wire:click="callTableColumnAction('{{ $name }}', '{{ $record->getKey() }}')"
            type="button"
            class="block text-left"
        >
            {{ $slot }}
        </button>
    @elseif ($url)
        <a
            href="{{ $url }}"
            {{ $shouldOpenUrlInNewTab ? 'target="_blank"' : null }}
            class="block"
        >
            {{ $slot }}
        </a>
    @else
        {{ $slot }}
    @endif
</td>
