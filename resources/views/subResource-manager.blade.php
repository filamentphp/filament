<div>
    @if (\Filament\Filament::can('viewAny', $this->getModel()))
        <x-filament::card class="space-y-4">
            <h3 class="text-lg font-medium leading-tight">
                {{ __(static::getTitle()) }}
            </h3>

            @livewire(static::getComponent('edit'), [
            'canUse'    => $this->canUse(),
            'canCreate' => $this->canCreate(),
            'canDelete' => $this->canDelete(),
            'manager' => static::class,
            'model' => $this->getModel(),
            'owner' => $this->getOwner(),
            ])
        </x-filament::card>
    @endif
</div>
