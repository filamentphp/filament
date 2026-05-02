@props([
    'action',
    'actionAlignment' => null,
    'afterItem' => null,
    'blocks',
    'columns' => null,
    'key',
    'trigger',
    'width' => null,
])

{!!
    \Filament\Forms\Components\Builder::renderBlockPickerHtml(
        action: $action,
        blocks: $blocks,
        key: $key,
        triggerHtml: $trigger instanceof \Illuminate\Contracts\Support\Htmlable ? $trigger->toHtml() : (string) $trigger,
        actionAlignment: $actionAlignment,
        afterItem: $afterItem,
        columns: $columns,
        width: $width,
    )
!!}
