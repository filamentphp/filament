@props([
    'actions' => [],
    'color' => null,
    'dropdownPlacement' => null,
    'dynamicComponent' => null,
    'group' => null,
    'icon' => null,
    'indicator' => null,
    'indicatorColor' => null,
    'label' => null,
    'size' => null,
    'tooltip' => null,
    'view' => null,
])

@if (! ($dynamicComponent && $group))
    {{
        \Filament\Actions\ActionGroup::make($actions)
            ->color($color)
            ->dropdownPlacement($dropdownPlacement)
            ->icon($icon)
            ->indicator($indicator)
            ->indicatorColor($indicatorColor)
            ->label($label)
            ->size($size)
            ->tooltip($tooltip)
            ->view($view)
    }}
@else
    <x-filament::dropdown
        :placement="$group->getDropdownPlacement() ?? 'bottom-start'"
        teleport
    >
        <x-slot name="trigger">
            <x-dynamic-component
                :component="$dynamicComponent"
                :color="$group->getColor()"
                :tooltip="$group->getTooltip()"
                :icon="$group->getIcon()"
                :indicator="$group->getIndicator()"
                :indicator-color="$group->getIndicatorColor()"
                :size="$group->getSize()"
                :label-sr-only="$group->isLabelHidden()"
                :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->merge($group->getExtraAttributes(), escape: false)"
            >
                {{ $slot }}
            </x-dynamic-component>
        </x-slot>

        <x-filament::dropdown.list>
            @foreach ($group->getActions() as $action)
                @if ($action->isVisible())
                    {{ $action }}
                @endif
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
@endif
