@props([
    'action' => null,
    'alignment' => null,
    'isClickDisabled' => false,
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
    @if ($isClickDisabled)
        {{ $slot }}
    @elseif ($url || ($recordUrl && $action === null))
        <a
            href="{{ $url ?: $recordUrl }}"
            {{ $shouldOpenUrlInNewTab ? 'target="_blank"' : null }}
            class="block"
        >
            {{ $slot }}
        </a>
    @elseif ($action || $recordAction)
        @php
            if ($action) {
                $wireClickAction = "callTableColumnAction('{$name}', '%s')";
            } else {
                if ($this->getCachedTableAction($recordAction)) {
                    $wireClickAction = "mountTableAction('{$recordAction}', '%s')";
                } else {
                    $wireClickAction = "{$recordAction}('%s')";
                }
            }

            $wireClickAction = sprintf($wireClickAction, $this->getTableRecordKey($record));
        @endphp

        <button
            wire:click="{{ $wireClickAction }}"
            wire:target="{{ $wireClickAction }}"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-70 cursor-wait"
            type="button"
            class="block text-left rtl:text-right w-full"
        >
            {{ $slot }}
        </button>
    @else
        {{ $slot }}
    @endif
</td>
