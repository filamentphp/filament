@props([
    'column',
    'isClickDisabled' => false,
    'record',
    'recordAction' => null,
    'recordKey' => null,
    'recordUrl' => null,
])

@php
    $action = $column->getAction();
    $alignment = $column->getAlignment();
    $name = $column->getName();
    $shouldOpenUrlInNewTab = $column->shouldOpenUrlInNewTab();
    $tooltip = $column->getTooltip();
    $url = $column->getUrl();

    $slot = $column->viewData(['recordKey' => $recordKey]);
@endphp

<div
    {{ $attributes->class([
        'filament-tables-column-wrapper',
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
            if ($action instanceof \Filament\Tables\Actions\Action) {
                $wireClickAction = "mountTableAction('{$action->getName()}', '{$recordKey}')";
            } elseif ($action) {
                $wireClickAction = "callTableColumnAction('{$name}', '{$recordKey}')";
            } else {
                if ($this->getCachedTableAction($recordAction)) {
                    $wireClickAction = "mountTableAction('{$recordAction}', '{$recordKey}')";
                } else {
                    $wireClickAction = "{$recordAction}('{$recordKey}')";
                }
            }
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
</div>
