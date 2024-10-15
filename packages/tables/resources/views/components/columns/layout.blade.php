@php
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'components',
    'record',
    'recordKey' => null,
    'rowLoop' => null,
])

@foreach ($components as $layoutComponent)
    @php
        $layoutComponent->record($record);
        $layoutComponent->rowLoop($rowLoop);
        $layoutComponent->recordKey($recordKey);
    @endphp

    @if ($layoutComponent->isVisible())
        <div
            {{
                (new ComponentAttributeBag)
                    ->gridColumn(
                        $layoutComponent->getColumnSpan(),
                        $layoutComponent->getColumnStart(),
                    )
                    ->class([
                        'fi-growable' => $layoutComponent->canGrow(),
                        (filled($layoutComponentHiddenFrom = $layoutComponent->getHiddenFrom()) ? "{$layoutComponentHiddenFrom}:fi-hidden" : ''),
                        (filled($layoutComponentVisibleFrom = $layoutComponent->getVisibleFrom()) ? "{$layoutComponentVisibleFrom}:fi-visible" : ''),
                    ])
            }}
        >
            @if ($layoutComponent instanceof \Filament\Tables\Columns\Column)
                @php
                    $layoutComponent->inline();

                    $layoutComponentAction = $layoutComponent->getAction();
                    $layoutComponentUrl = $layoutComponent->getUrl();
                    $isLayoutComponentClickDisabled = $layoutComponent->isClickDisabled();

                    $layoutComponentWrapperTag = match (true) {
                        $layoutComponentUrl && (! $isLayoutComponentClickDisabled) => 'a',
                        $layoutComponentAction && (! $isLayoutComponentClickDisabled) => 'button',
                        default => 'div',
                    };

                    if ($layoutComponentWrapperTag === 'button') {
                        if ($layoutComponentAction instanceof \Filament\Actions\Action) {
                            $layoutComponentWireClickAction = "mountTableAction('{$layoutComponentAction->getName()}', '{$recordKey}')";
                        } elseif ($layoutComponentAction) {
                            $layoutComponentWireClickAction = "callTableColumnAction('{$layoutComponent->getName()}', '{$recordKey}')";
                        }
                    }
                @endphp

                <{{ $layoutComponentWrapperTag }}
                    @if (filled($layoutComponentTooltip = $layoutComponent->getTooltip()))
                        x-tooltip="{
                            content: @js($layoutComponentTooltip),
                            theme: $store.theme,
                        }"
                    @endif
                    @if ($layoutComponentWrapperTag === 'a')
                        {{ \Filament\Support\generate_href_html($layoutComponentUrl, $layoutComponent->shouldOpenUrlInNewTab()) }}
                    @elseif ($layoutComponentWrapperTag === 'button')
                        type="button"
                        wire:click="{{ $layoutComponentWireClickAction }}"
                        wire:loading.attr="disabled"
                        wire:target="{{ $layoutComponentWireClickAction }}"
                    @endif
                    @class([
                        'fi-ta-col-wrp',
                        ((($layoutComponentAlignment = $layoutComponent->getAlignment()) instanceof \Filament\Support\Enums\Alignment) ? "fi-align-{$layoutComponentAlignment->value}" : (is_string($layoutComponentAlignment) ? $layoutComponentAlignment : '')),
                        'fi-ta-col-wrap-has-column-url' => ($layoutComponentWrapperTag === 'a') && filled($layoutComponentUrl),
                    ])
                >
                    {{ $layoutComponent }}
                </{{ $layoutComponentWrapperTag }}>
            @else
                {{ $layoutComponent }}
            @endif
        </div>
    @endif
@endforeach
