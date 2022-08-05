<x-tables::dropdown.item
    :x-on:click="'mountBulkAction(\'' . $getName() . '\')'"
    :icon="$getIcon()"
    :color="$getColor()"
    class="filament-tables-bulk-action"
>
    {{ $getLabel() }}
</x-tables::dropdown.item>
