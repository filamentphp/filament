<x-filament::page
    :widget-data="['record' => $record]"
    :class="\Illuminate\Support\Arr::toCssClasses([
        'filament-resources-create-record-page',
        'filament-resources-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'filament-resources-record-' . $record->getKey(),
    ])"
>
    @capture($form, $livewire)
        <x-filament::form wire:submit.prevent="save">
            {{ $livewire->form }}

            <x-filament::form.actions
                :actions="$livewire->getCachedFormActions()"
                :full-width="$livewire->hasFullWidthFormActions()"
            />
        </x-filament::form>
    @endcapture

    @php
        $relationManagers = $this->getRelationManagers();
    @endphp

    @if ((! $this->hasCombinedRelationManagersWithForm()) || (! count($relationManagers)))
        {{ $form($this) }}
    @endif

    @if (count($relationManagers))
        @if (! $this->hasCombinedRelationManagersWithForm())
            <x-filament::hr />
        @endif

        <x-filament::resources.relation-managers
            :active-manager="$activeRelationManager"
            :form-tab-label="$this->getFormTabLabel()"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        >
            @if ($this->hasCombinedRelationManagersWithForm())
                <x-slot name="form">
                    {{ $form($this) }}
                </x-slot>
            @endif
        </x-filament::resources.relation-managers>
    @endif
</x-filament::page>
