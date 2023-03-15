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

    $alignmentClass = match ($alignment) {
        'center' => 'text-center justify-center',
        'end' => 'text-end justify-end',
        'left' => 'text-left justify-start',
        'right' => 'text-right justify-end',
        'justify' => 'text-justify justify-between',
        default => 'text-start justify-start',
    };

    $slot = $column->viewData(['recordKey' => $recordKey]);
@endphp

<div
    @if ($tooltip)
        x-data="{}"
        x-tooltip.raw="{{ $tooltip }}"
    @endif
    {{ $attributes->class(['filament-tables-column-wrapper']) }}
>
    @if (($url || ($recordUrl && $action === null)) && (! $isClickDisabled))
        <a
            href="{{ $url ?: $recordUrl }}"
            {!! $shouldOpenUrlInNewTab ? 'target="_blank"' : null !!}
            @class([
                'block w-full flex',
                $alignmentClass,
            ])
        >
            {{ $slot }}
        </a>
    @elseif (($action || $recordAction) && (! $isClickDisabled))
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
            @class([
                'block w-full flex',
                $alignmentClass,
            ])
        >
            {{ $slot }}
        </button>
    @else
        <div @class([
            'flex',
            $alignmentClass,
        ])>
            {{ $slot }}
        </div>
    @endif
</div>
