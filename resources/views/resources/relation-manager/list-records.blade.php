<div class="space-y-4">
    <div class="items-center justify-between space-y-4 lg:flex lg:space-y-0 lg:space-x-4">
        <div class="space-x-3 flex-shink-0">
            @if ($this->canCreate())
                <x-filament::button wire:click="openCreate">
                    {{ __($this->getManager()::$createButtonLabel) }}
                </x-filament::button>
            @endif

            @if ($this->canAttach())
                <x-filament::button wire:click="openAttach">
                    {{ __($this->getManager()::$attachButtonLabel) }}
                </x-filament::button>
            @endif

            @if ($this->canDetach())
                <x-filament::button
                    wire:click="openDetach"
                    color="danger"
                    :disabled="count($selected) === 0"
                >
                    {{ __($this->getManager()::$detachButtonLabel) }}
                </x-filament::button>
            @endif

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

    @if ($this->canDetach())
        <x-filament::modal
            :name="$this->getManager() . 'RelationManagerDetachModal'"
        >
            <x-filament::card class="space-y-5">
                <x-filament::card-header :title="$this->getManager()::$detachModalHeading">
                    <p class="text-sm text-gray-500">
                        {{ __($this->getManager()::$detachModalDescription) }}
                    </p>
                </x-filament::card-header>

                <div class="space-y-3 sm:space-y-0 sm:space-x-3 sm:flex sm:justify-end">
                    <x-filament::button x-on:click="$dispatch('close', '{{ (string) Str::of($this->getManager())->replace('\\', '\\\\') }}RelationManagerDetachModal')">
                        {{ __($this->getManager()::$detachModalCancelButtonLabel) }}
                    </x-filament::button>

                    <x-filament::button
                        type="button"
                        color="danger"
                        wire:click="detachSelected"
                    >
                        {{ __($this->getManager()::$detachModalDetachButtonLabel) }}
                    </x-filament::button>
                </div>
            </x-filament::card>
        </x-filament::modal>
    @endif
</div>
