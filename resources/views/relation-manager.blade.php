<div class="space-y-4">
    <div class="lg:flex items-center space-y-4 lg:space-y-0 lg:space-x-4 justify-between">
        <div class="flex-shink-0 space-x-3">
            <x-filament::button wire:click="openCreate">
                Add Product
            </x-filament::button>

            <x-filament::button color="danger">Remove 1 Product</x-filament::button>
        </div>

        <x-tables::filter :table="$this->getTable()" />
    </div>

    <x-tables::table
        :records="$records"
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
                'cancelButtonLabel' => static::$createModalCancelButtonLabel,
                'createButtonLabel' => static::$createModalCreateButtonLabel,
                'createdMessage' => static::$createdMessage,
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
                'cancelButtonLabel' => static::$editModalCancelButtonLabel,
                'manager' => static::class,
                'owner' => $this->owner,
                'saveButtonLabel' => static::$editModalSaveButtonLabel,
                'savedMessage' => static::$savedMessage,
            ])
        </x-filament::card>
    </x-filament::modal>
</div>
