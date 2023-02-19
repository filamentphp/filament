<tr>
    <x-tables::loading-cell
        :colspan="$columnsCount"
        :wire:key="$this->id . '.table.defer-loading-indicator'"
    />
</tr>