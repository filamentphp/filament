<x-filament::global-search.actions.action
    :action="$action"
    component="filament::button"
    :outlined="$isOutlined()"
    :icon-position="$getIconPosition()"
    class="filament-global-search-button-action"
>
    {{ $getLabel() }}
</x-filament::global-search.actions.action>
