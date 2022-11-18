<x-filament::dropdown
    :placement="$getDropdownPlacement() ?? 'bottom-end'"
    teleport
    {{ $attributes }}
>
    <x-slot name="trigger">
        <x-filament::icon-button
            :color="$getColor()"
            :icon="$getIcon() ?? 'heroicon-m-ellipsis-vertical'"
            :size="$getSize()"
            :tooltip="$getTooltip()"
        >
            <x-slot name="label">
                {{ $getLabel() ?? __('filament-actions::group.trigger.label') }}
            </x-slot>
        </x-filament::icon-button>
    </x-slot>

    <x-filament::dropdown.list>
        @foreach ($getActions() as $action)
            @if (! $action->isHidden())
                {{ $action }}
            @endif
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
