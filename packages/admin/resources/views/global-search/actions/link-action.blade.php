<x-filament::global-search.actions.action
    :action="$action"
    component="filament::link"
    :icon-position="$getIconPosition()"
    class="filament-global-search-link-action"
>
    {{ $getLabel() }}
</x-filament::global-search.actions.action>
