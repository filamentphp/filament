<x-filament::page
    @class([
        'fi-resources-edit-record-page',
        'fi-resources-' . str_replace('/', '-', $this->getResource()::getSlug()),
        'fi-resources-record-' . $record->getKey(),
    ])
>
    @capture($form)
        <x-filament::form
            wire:submit="save"
            :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
        >
            {{ $this->form }}

            <x-filament::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        </x-filament::form>
    @endcapture

    @php
        $relationManagers = $this->getRelationManagers();
    @endphp

    @if ((! $this->hasCombinedRelationManagerTabsWithContent()) || (! count($relationManagers)))
        {{ $form() }}
    @endif

    @if (count($relationManagers))
        @if (! $this->hasCombinedRelationManagerTabsWithContent())
            <x-filament::hr />
        @endif

        <x-filament::resources.relation-managers
            :active-locale="isset($activeLocale) ? $activeLocale : null"
            :active-manager="$activeRelationManager"
            :content-tab-label="$this->getContentTabLabel()"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        >
            @if ($this->hasCombinedRelationManagerTabsWithContent())
                <x-slot name="content">
                    {{ $form() }}
                </x-slot>
            @endif
        </x-filament::resources.relation-managers>
    @endif
</x-filament::page>
