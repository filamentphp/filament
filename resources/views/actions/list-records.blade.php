<div>
    <x-filament::app-header :title="__($title)">
        <x-slot name="actions">
            <x-filament::button
                color="primary"
                :href="$this->getResource()::route($createRoute)"
            >
                Create
            </x-filament::button>
        </x-slot>
    </x-filament::app-header>

    <x-filament::app-content>
        <x-tables::container
            :columns="$this->getTable()->getVisibleColumns()"
            :link="fn($record) => $this->getResource()::route('edit', ['record' => $record->id])"
            :records="$records"
            :sort-column="$sortColumn"
            :sort-direction="$sortDirection"
        />
    </x-filament::app-content>
</div>
