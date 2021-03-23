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

    <x-filament::app-content class="space-y-4">
        <div class="items-center justify-between space-y-4 sm:flex sm:space-y-0">
            <div>
                @if ($this->canDelete())
                    <x-tables::delete-selected :disabled="! $this->canDeleteSelected()" />
                @endif
            </div>

            <x-tables::filter :table="$this->getTable()" />
        </div>

        <x-tables::table
            :records="$records"
            :selected="$selected"
            :sort-column="$sortColumn"
            :sort-direction="$sortDirection"
            :table="$this->getTable()"
        />

        @if ($this->hasPagination())
            <x-tables::pagination.paginator :paginator="$records" />
        @endif
    </x-filament::app-content>
</div>
