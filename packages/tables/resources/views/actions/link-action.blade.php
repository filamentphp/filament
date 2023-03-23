<x-tables::actions.action
    :action="$action"
    component="tables::link"
    :icon-position="$getIconPosition()"
    class="filament-tables-link-action"
>
    <span @class([
        'sr-only' => $isLabelHidden(),
    ])>
        {{ $getLabel() }}
    </span>
</x-tables::actions.action>
