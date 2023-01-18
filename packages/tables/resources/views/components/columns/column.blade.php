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
            'start' => 'text-start',
            'center' => 'text-center',
            'end' => 'text-end',
            'left' => 'text-left',
            'right' => 'text-right',
            'justify' => 'text-justify',
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
            {!! $shouldOpenUrlInNewTab ? 'target="_blank"' : null !!}
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
            wire:loading.class="cursor-wait opacity-70"
            type="button"
            class="block w-full text-start"
        >
            {{ $slot }}
        </button>
    @else
        {{ $slot }}
    @endif
</div>
