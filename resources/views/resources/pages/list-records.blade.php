<div>
    <x-filament::app-header :title="$title">
        <x-slot name="actions">
            @if ($this->canCreate())
                <x-filament::button
                    color="primary"
                    :href="static::getResource()::generateUrl($createRoute)"
                >
                    {{ __(static::$createButtonLabel) }}
                </x-filament::button>
            @endif
        </x-slot>
    </x-filament::app-header>

    <x-filament::app-content>
        <x-tables::container
            :records="$records"
            :selected="$selected"
            :sort-column="$sortColumn"
            :sort-direction="$sortDirection"
            :table="$this->getTable()"
        />
    </x-filament::app-content>
</div>
