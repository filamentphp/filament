<x-filament::page
    :widget-data="['record' => $record]"
    :class="\Illuminate\Support\Arr::toCssClasses([
        'filament-resources-view-record-page',
        'filament-resources-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'filament-resources-record-' . $record->getKey(),
    ])"
>
    @php
        $relationManagers = $this->getRelationManagers();
    @endphp

    @if ((! $this->hasCombinedRelationManagersWithForm()) || (! count($relationManagers)))
        {{ $this->form }}
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
                    {{ $this->form }}
                </x-slot>
            @endif
        </x-filament::resources.relation-managers>
    @endif
</x-filament::page>
