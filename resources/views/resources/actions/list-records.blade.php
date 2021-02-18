<div>
    <x-filament::app-header :title="__($title)">
        <x-slot name="actions">
            <x-filament::button
                color="primary"
                :href="$this->getResource()::generateUrl($createRoute)"
            >
                Create
            </x-filament::button>
        </x-slot>
    </x-filament::app-header>

    <x-filament::app-content>
        <x-tables::container
            :records="$records"
            :sort-column="$sortColumn"
            :sort-direction="$sortDirection"
            :table="$this->getTable()"
        />
    </x-filament::app-content>
</div>
