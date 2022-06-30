@props([
    'action' => null,
    'alignment' => null,
    'name',
    'record',
    'recordAction' => null,
    'recordUrl' => null,
    'shouldOpenUrlInNewTab' => false,
    'tooltip' => null,
    'url' => null,
])

<td
    {{ $attributes->class([
        'filament-tables-cell',
        'dark:text-white' => config('tables.dark_mode'),
        match ($alignment) {
            'left' => 'text-left',
            'center' => 'text-center',
            'right' => 'text-right',
            default => null,
        },
    ]) }}
    @if ($tooltip)
        x-data="{}"
        x-tooltip.raw="{{ $tooltip }}"
    @endif
>
    @if ($action || ($recordAction && $url === null))
        <button
            wire:click="{{ $action ? "callTableColumnAction('{$name}', " : "{$recordAction}(" }}'{{ $this->getTableRecordKey($record) }}')"
            wire:target="{{ $action ? "callTableColumnAction('{$name}', " : "{$recordAction}(" }}'{{ $this->getTableRecordKey($record) }}')"
            wire:loading.attr.delay="disabled"
            wire:loading.class.delay="opacity-70 cursor-wait"
            type="button"
            class="block text-left"
        >
            {{ $slot }}
        </button>
    @elseif ($url || ($recordUrl && $url === null))
        <a
            href="{{ $url ?: $recordUrl }}"
            {{ $shouldOpenUrlInNewTab ? 'target="_blank"' : null }}
            class="block"
        >
            {{ $slot }}
        </a>
    @else
        {{ $slot }}
    @endif
</td>
