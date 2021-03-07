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

@if($column->action)
    <button
        wire:click="{{ $column->action }}('{{ $record->getKey() }}')"
        type="button"
    >
        <x-dynamic-component :component="$iconToShow" class="{{ $classes ?? '' }} w-6 h-6" />
    </button>
@else
    <x-dynamic-component :component="$iconToShow" class="{{ $classes ?? '' }} w-6 h-6" />
@endif
