@php
    $iconToShow = null;

    foreach ($column->options as $icon => $callback) {
        if (! $callback($column->getValue($record))) {
            continue;
        }

        $iconToShow = $icon;

        break;
    }
@endphp

@if ($iconToShow)
    @if ($column->action)
        <button
            wire:click="{{ $column->action }}('{{ $record->getKey() }}')"
            type="button"
        >
            <x-dynamic-component :component="$iconToShow" class="{{ $classes ?? null }} w-6 h-6" />
        </button>
    @elseif ($column->url)
        <a
            href="{{ $column->getUrl($record) }}"
            @if ($column->shouldOpenUrlInNewTab)
            target="_blank"
            rel="noopener noreferrer"
            @endif
        >
            <x-dynamic-component :component="$iconToShow" class="{{ $classes ?? null }} w-6 h-6" />
        </a>
    @else
        <x-dynamic-component :component="$iconToShow" class="{{ $classes ?? null }} w-6 h-6" />
    @endif
@endif
