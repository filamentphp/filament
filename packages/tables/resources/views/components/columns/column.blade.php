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
    $alignment = $column->getAlignment() ?? Alignment::Start;
    $name = $column->getName();
    $shouldOpenUrlInNewTab = $column->shouldOpenUrlInNewTab();
    $tooltip = $column->getTooltip();
    $url = $column->getUrl();

    if (! $alignment instanceof Alignment) {
        $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }

    $columnClasses = \Illuminate\Support\Arr::toCssClasses([
        'flex w-full disabled:pointer-events-none',
        match ($alignment) {
            Alignment::Start => 'justify-start text-start',
            Alignment::Center => 'justify-center text-center',
            Alignment::End => 'justify-end text-end',
            Alignment::Left => 'justify-start text-left',
            Alignment::Right => 'justify-end text-right',
            Alignment::Justify => 'justify-between text-justify',
            default => $alignment,
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
