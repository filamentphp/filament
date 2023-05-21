@props([
    'actions' => [],
    'button' => false,
    'color' => null,
    'divided' => false,
    'dropdownPlacement' => null,
    'dynamicComponent' => null,
    'group' => null,
    'icon' => null,
    'iconButton' => false,
    'indicator' => null,
    'indicatorColor' => null,
    'label' => null,
    'link' => false,
    'size' => null,
    'tooltip' => null,
    'view' => null,
])

@if (! ($dynamicComponent && $group))
    @php
        $group = \Filament\Actions\ActionGroup::make($actions)
            ->color($color)
            ->divided($divided)
            ->dropdownPlacement($dropdownPlacement)
            ->icon($icon)
            ->indicator($indicator)
            ->indicatorColor($indicatorColor)
            ->label($label)
            ->size($size)
            ->tooltip($tooltip)
            ->view($view);

        if ($button) {
            $group->button();
        }

        if ($iconButton) {
            $group->iconButton();
        }

        if ($link) {
            $group->link();
        }
    @endphp

    {{ $group }}
@elseif (! $group->hasDropdown())
    @foreach ($group->getActions() as $action)
        @if ($action->isVisible())
            {{ $action }}
        @endif
    @endforeach
@else
    @php
        $actionLists = [];
        $singleActions = [];

        foreach ($group->getActions() as $action) {
            if ($action->isHidden()) {
                continue;
            }

            if ($action instanceof \Filament\Actions\ActionGroup && (! $action->hasDropdown())) {
                if (count($singleActions)) {
                    $actionLists[] = $singleActions;
                    $singleActions = [];
                }

                $actionLists[] = array_filter(
                    $action->getActions(),
                    fn ($action): bool => $action->isVisible(),
                );
            } else {
                $singleActions[] = $action;
            }
        }

        if (count($singleActions)) {
            $actionLists[] = $singleActions;
        }
    @endphp

    <x-filament::dropdown
        :max-height="$group->getDropdownMaxHeight()"
        :placement="$group->getDropdownPlacement() ?? 'bottom-start'"
        :width="$group->getDropdownWidth()"
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

        @foreach ($actionLists as $actions)
            <x-filament::dropdown.list>
                @foreach ($actions as $action)
                    {{ $action }}
                @endforeach
            </x-filament::dropdown.list>
        @endforeach
    </x-filament::dropdown>
@endif
