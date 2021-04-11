<div>
    @if (\Filament\Filament::can('viewAny', $this->getModel()))
        <x-filament::card class="space-y-4">
            <h3 class="text-lg font-medium leading-tight">
                {{ __(static::getTitle()) }}
            </h3>

            @livewire(static::getComponent('list'), [
                'canAttach' => $this->canAttach(),
                'canCreate' => $this->canCreate(),
                'canDelete' => $this->canDelete(),
                'canDetach' => $this->canDetach(),
                'manager' => static::class,
                'model' => $this->getModel(),
                'owner' => $this->getOwner(),
            ])
        </x-filament::card>

        @if ($this->canCreate())
            <x-filament::modal
                class="w-full max-w-4xl"
                :name="static::class . 'RelationManagerCreateModal'"
            >
                <x-filament::card class="space-y-5">
                    <x-filament::card-header :title="static::$createModalHeading" />

                    @livewire(static::getComponent('create'), [
                        'manager' => static::class,
                        'model' => $this->getModel(),
                        'owner' => $this->getOwner(),
                    ])
                </x-filament::card>
            </x-filament::modal>
        @endif

        @if ($this->canEdit())
            <x-filament::modal
                class="w-full max-w-4xl"
                :name="static::class . 'RelationManagerEditModal'"
            >
                <x-filament::card class="space-y-5">
                    <x-filament::card-header :title="static::$editModalHeading" />

                    @livewire(static::getComponent('edit'), [
                        'manager' => static::class,
                        'model' => $this->getModel(),
                        'owner' => $this->getOwner(),
                    ])
                </x-filament::card>
            </x-filament::modal>
        @endif

        @if ($this->canAttach())
            <x-filament::modal
                class="w-full max-w-lg"
                :name="static::class . 'RelationManagerAttachModal'"
            >
                <x-filament::card class="w-full space-y-5">
                    <x-filament::card-header :title="static::$attachModalHeading" />

                    @livewire(static::getComponent('attach'), [
                        'manager' => static::class,
                        'owner' => $this->getOwner(),
                    ])
                </x-filament::card>
            </x-filament::modal>
        @endif
    @endif
</div>
