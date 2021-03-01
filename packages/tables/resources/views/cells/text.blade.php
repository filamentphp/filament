@php
    $primaryClasses = $column->primary ? 'font-medium' : null;
@endphp

@if ($column->action)
    <button
        wire:click="{{ $column->action }}('{{ $record->getKey() }}')"
        type="button"
        class="{{ $primaryClasses }} hover:underline hover:text-secondary-700 transition-colors duration-200"
    >
        {{ $column->getValue($record) }}
    </button>
@elseif ($column->url)
    <a
        href="{{ $column->getUrl($record) }}"
        class="{{ $primaryClasses }} hover:underline hover:text-secondary-700 transition-colors duration-200"
    >
        {{ $column->getValue($record) }}
    </a>
@else
    <span class="{{ $primaryClasses }}">{{ $column->getValue($record) }}</span>
@endunless
