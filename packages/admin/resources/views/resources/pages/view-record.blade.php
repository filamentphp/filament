<x-filament::page :widget-data="['record' => $record]" class="filament-resources-view-record-page">
    {{ $this->form }}
    
    @if (count($relationManagers = $this->getRelationManagers()))

        @if (is_array($relationManagers[0]))
            @foreach ($relationManagers as $relationManagersGroup)
                <x-filament::hr />
                <x-filament::resources.relation-managers :active-manager="$activeRelationManager" :managers="$relationManagersGroup" :owner-record="$record" />
            @endforeach
        @else
            <x-filament::hr />
            <x-filament::resources.relation-managers :active-manager="$activeRelationManager" :managers="$relationManagers" :owner-record="$record" />
        @endif
    @endif
</x-filament::page>
