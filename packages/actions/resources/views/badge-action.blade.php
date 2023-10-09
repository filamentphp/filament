<x-filament-actions::action
    :action="$action"
    dynamic-component="filament::badge"
    :icon-position="$getIconPosition()"
    class="fi-ac-badge-action"
>
    {{ $getLabel() }}
</x-filament-actions::action>
