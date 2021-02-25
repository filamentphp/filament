<div class="space-y-4">
    <h3 class="text-lg leading-tight font-medium">
        {{ __(static::getTitle()) }}
    </h3>

    <div class="lg:flex items-center space-y-4 lg:space-y-0 lg:space-x-4 justify-between">
        <div class="flex-shink-0 space-x-3">
            <x-filament::button wire:click="openCreate">
                {{ __(static::$createButtonLabel) }}
            </x-filament::button>

            <x-tables::delete-selected :selected="$selected" />
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

    <x-filament::modal
        :name="static::class.'RelationManagerCreateModal'"
    >
        <x-filament::card class="space-y-5 max-w-4xl">
            <x-filament::card-header :title="__(static::$createModalHeading)" />

            @livewire(Filament\Resources\RelationManager\CreateRecord::class, [
                'cancelButtonLabel' => __(static::$createModalCancelButtonLabel),
                'createButtonLabel' => __(static::$createModalCreateButtonLabel),
                'createdMessage' => __(static::$createdMessage),
                'manager' => static::class,
                'owner' => $this->owner,
            ])
        </x-filament::card>
    </x-filament::modal>

    <x-filament::modal
        :name="static::class.'RelationManagerEditModal'"
    >
        <x-filament::card class="space-y-5 max-w-4xl">
            <x-filament::card-header :title="__(static::$editModalHeading)" />

            @livewire(Filament\Resources\RelationManager\EditRecord::class, [
                'cancelButtonLabel' => __(static::$editModalCancelButtonLabel),
                'manager' => static::class,
                'owner' => $this->owner,
                'saveButtonLabel' => __(static::$editModalSaveButtonLabel),
                'savedMessage' => __(static::$savedMessage),
            ])
        </x-filament::card>
    </x-filament::modal>
</div>
