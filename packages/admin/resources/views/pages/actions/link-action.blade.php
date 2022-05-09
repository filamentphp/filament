<x-filament::pages.actions.action
    :action="$action"
    component="filament::link"
    :icon-position="$getIconPosition()"
    class="filament-page-link-action"
>
    {{ $getLabel() }}
</x-filament::pages.actions.action>
