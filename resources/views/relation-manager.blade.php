<div class="space-y-4">
    <h3 class="text-lg font-medium leading-tight">
        {{ __(static::getTitle()) }}
    </h3>

    <div class="items-center justify-between space-y-4 lg:flex lg:space-y-0 lg:space-x-4">
        <div class="space-x-3 flex-shink-0">
            <x-filament::button wire:click="openCreate">
                {{ __(static::$createButtonLabel) }}
            </x-filament::button>

            @unless ($this->isHasMany())
                <x-filament::button wire:click="openAttach">
                    {{ __(static::$attachButtonLabel) }}
                </x-filament::button>
            @endunless

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

    @unless ($this->isHasMany())
        <x-filament::modal
            class="w-full max-w-lg"
            :name="static::class.'RelationManagerAttachModal'"
        >
            <x-filament::card class="space-y-5">
                <x-filament::card-header :title="__(static::$attachModalHeading)" />

                @livewire(\Livewire\Livewire::getAlias(Filament\Resources\RelationManager\AttachRecord::class), [
                    'cancelButtonLabel' => __(static::$attachModalCancelButtonLabel),
                    'attachAnotherButtonLabel' => __(static::$attachModalAttachAnotherButtonLabel),
                    'attachButtonLabel' => __(static::$attachModalAttachButtonLabel),
                    'attachedMessage' => __(static::$attachModalAttachedMessage),
                    'manager' => static::class,
                    'owner' => $this->owner,
                ])
            </x-filament::card>
        </x-filament::modal>
    @endunless

    <x-filament::modal
        class="w-full max-w-4xl"
        :name="static::class.'RelationManagerCreateModal'"
    >
        <x-filament::card class="space-y-5">
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
        class="w-full max-w-4xl"
        :name="static::class.'RelationManagerEditModal'"
    >
        <x-filament::card class="space-y-5">
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
