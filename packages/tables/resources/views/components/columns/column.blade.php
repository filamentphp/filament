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
    @if ($tooltip)
        x-data="{}"
        x-tooltip.raw="{{ $tooltip }}"
    @endif
    {{ $attributes->class([
        'filament-tables-column-wrapper',
        match ($alignment) {
            'center' => 'text-center',
            'end' => 'text-end',
            'justify' => 'text-justify',
            'left' => 'text-left',
            'right' => 'text-right',
            'start' => 'text-start',
            default => null,
        },
    ]) }}
>
    @if ($isClickDisabled)
        {{ $slot }}
    @elseif ($url || ($recordUrl && $action === null))
        <a
            href="{{ $url ?: $recordUrl }}"
            @if ($shouldOpenUrlInNewTab) target="_blank" @endif
            class="inline-block"
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
                if ($this->getTable()->getAction($recordAction)) {
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
            type="button"
            class="inline-block disabled:opacity-70 disabled:pointer-events-none"
        >
            {{ $slot }}
        </button>
    @else
        {{ $slot }}
    @endif
</div>
