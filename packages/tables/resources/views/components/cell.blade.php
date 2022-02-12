@props([
'action' => null,
'name',
'record',
'shouldOpenUrlInNewTab' => false,
'url' => null,
'alignment' => null,
])

@php
    $alignmentClasses =  \Illuminate\Support\Arr::toCssClasses([
        'text-left' => $alignment === 'left',
        'text-center' => $alignment === 'center',
        'text-right' => $alignment === 'right',
    ]);
@endphp

<td {{ $attributes->class(['filament-tables-cell', 'dark:text-white' => config('tables.dark_mode'), $alignmentClasses]) }}>
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
