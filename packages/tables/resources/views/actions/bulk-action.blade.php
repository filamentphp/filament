<x-tables::dropdown.list.item
    :x-on:click="'mountBulkAction(\'' . $getName() . '\')'"
    :icon="$getIcon()"
    :color="$getColor()"
    :attributes="\Filament\Support\prepare_inherited_attributes($getExtraAttributeBag())"
    class="filament-tables-bulk-action"
>
    {{ $getLabel() }}
</x-tables::dropdown.list.item>
