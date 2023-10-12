@props([
    'column',
    'isClickDisabled' => false,
    'record',
    'recordAction' => null,
    'recordKey' => null,
    'recordUrl' => null,
])

@php
    use Filament\Support\Enums\Alignment;

    $action = $column->getAction();
    $name = $column->getName();
    $shouldOpenUrlInNewTab = $column->shouldOpenUrlInNewTab();
    $tooltip = $column->getTooltip();
    $url = $column->getUrl();

    $columnClasses = \Illuminate\Support\Arr::toCssClasses([
        'flex w-full disabled:pointer-events-none',
        match ($column->getAlignment()) {
            Alignment::Center, 'center' => 'justify-center text-center',
            Alignment::End, 'end' => 'justify-end text-end',
            Alignment::Left, 'left' => 'justify-start text-left',
            Alignment::Right, 'right' => 'justify-end text-right',
            Alignment::Justify, 'justify' => 'justify-between text-justify',
            default => 'justify-start text-start',
        },
    ]);

    $slot = $column->viewData(['recordKey' => $recordKey]);
@endphp

<div
    @if (filled($tooltip))
        x-data="{}"
        x-tooltip="{
            content: @js($tooltip),
            theme: $store.theme,
        }"
    @endif
    {{ $attributes->class(['fi-ta-col-wrp']) }}
>
    @if (($url || ($recordUrl && $action === null)) && (! $isClickDisabled))
        <a
            {{ \Filament\Support\generate_href_html($url ?: $recordUrl, $shouldOpenUrlInNewTab) }}
            class="{{ $columnClasses }}"
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
                if ($this->getTable()->getAction($recordAction)) {
                    $wireClickAction = "mountTableAction('{$recordAction}', '{$recordKey}')";
                } else {
                    $wireClickAction = "{$recordAction}('{$recordKey}')";
                }
            }
        @endphp

        <button
            type="button"
            wire:click="{{ $wireClickAction }}"
            wire:loading.attr="disabled"
            wire:target="{{ $wireClickAction }}"
            class="{{ $columnClasses }}"
        >
            {{ $slot }}
        </button>
    @else
        <div class="{{ $columnClasses }}">
            {{ $slot }}
        </div>
    @endif
</div>
