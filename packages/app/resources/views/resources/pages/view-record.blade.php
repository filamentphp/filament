<x-filament::page
    :widget-data="['record' => $record]"
    @class([
        'filament-resources-view-record-page',
        'filament-resources-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'filament-resources-record-' . $record->getKey(),
    ])
>
    @php
        $relationManagers = $this->getRelationManagers();
    @endphp

    @if ((! $this->hasCombinedRelationManagerTabsWithContent()) || (! count($relationManagers)))
        @if ($this->hasInfolist())
            {{ $this->getCachedInfolist() }}
        @else
            {{ $this->form }}
        @endif
    @endif

    @if (count($relationManagers))
        @if (! $this->hasCombinedRelationManagerTabsWithContent())
            <x-filament::hr />
        @endif

        <x-filament::resources.relation-managers
            :active-manager="$activeRelationManager"
            :content-tab-label="$this->getContentTabLabel()"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        >
            @if ($this->hasCombinedRelationManagerTabsWithContent())
                <x-slot name="content">
                    @if ($this->hasInfolist())
                        {{ $this->getCachedInfolist() }}
                    @else
                        {{ $this->form }}
                    @endif
                </x-slot>
            @endif
        </x-filament::resources.relation-managers>
    @endif
</x-filament::page>
