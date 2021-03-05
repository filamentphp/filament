<div class="space-y-4">
    <h3 class="text-lg font-medium leading-tight">
        {{ __(static::getTitle()) }}
    </h3>

    <div class="items-center justify-between space-y-4 lg:flex lg:space-y-0 lg:space-x-4">
        <div class="space-x-3 flex-shink-0">
            <x-filament::button wire:click="openCreate">
                {{ __(static::$createButtonLabel) }}
            </x-filament::button>

            <x-filament::button wire:click="openAdd">
                {{ __(static::$addButtonLabel) }}
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
        <x-filament::card class="w-full max-w-4xl space-y-5">
            <x-filament::card-header :title="__(static::$createModalHeading)" />

            @livewire(\Livewire\Livewire::getAlias(Filament\Resources\RelationManager\CreateRecord::class), [
                'cancelButtonLabel' => __(static::$createModalCancelButtonLabel),
                'createButtonLabel' => __(static::$createModalCreateButtonLabel),
                'createdMessage' => __(static::$createModalCreatedMessage),
                'manager' => static::class,
                'owner' => $this->owner,
            ])
        </x-filament::card>
    </x-filament::modal>

    <x-filament::modal
        :name="static::class.'RelationManagerAddModal'"
    >
        <x-filament::card class="w-full max-w-4xl space-y-5">
            <x-filament::card-header :title="__(static::$addModalHeading)" />

            @livewire(\Livewire\Livewire::getAlias(Filament\Resources\RelationManager\AddRecord::class), [
                'cancelButtonLabel' => __(static::$addModalCancelButtonLabel),
                'addButtonLabel' => __(static::$addModalAddButtonLabel),
                'addedMessage' => __(static::$addModalAddedMessage),
                'manager' => static::class,
                'owner' => $this->owner,
            ])
        </x-filament::card>
    </x-filament::modal>

    <x-filament::modal
        :name="static::class.'RelationManagerEditModal'"
    >
        <x-filament::card class="w-full max-w-4xl space-y-5">
            <x-filament::card-header :title="__(static::$editModalHeading)" />

            @livewire(\Livewire\Livewire::getAlias(Filament\Resources\RelationManager\EditRecord::class), [
                'cancelButtonLabel' => __(static::$editModalCancelButtonLabel),
                'manager' => static::class,
                'owner' => $this->owner,
                'saveButtonLabel' => __(static::$editModalSaveButtonLabel),
                'savedMessage' => __(static::$editModalSavedMessage),
            ])
        </x-filament::card>
    </x-filament::modal>
</div>
