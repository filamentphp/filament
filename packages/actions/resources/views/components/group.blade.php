@props([
    'actions' => [],
    'badge' => null,
    'badgeColor' => null,
    'button' => false,
    'color' => null,
    'dropdownMaxHeight' => null,
    'dropdownOffset' => null,
    'dropdownPlacement' => null,
    'dropdownWidth' => null,
    'dynamicComponent' => null,
    'group' => null,
    'icon' => null,
    'iconSize' => null,
    'iconButton' => false,
    'label' => null,
    'link' => false,
    'size' => null,
    'tooltip' => null,
    'view' => null,
])

@if (! ($dynamicComponent && $group))
    @php
        $group = \Filament\Actions\ActionGroup::make($actions)
            ->badgeColor($badgeColor)
            ->color($color)
            ->dropdownMaxHeight($dropdownMaxHeight)
            ->dropdownOffset($dropdownOffset)
            ->dropdownPlacement($dropdownPlacement)
            ->dropdownWidth($dropdownWidth)
            ->icon($icon)
            ->iconSize($iconSize)
            ->label($label)
            ->size($size)
            ->tooltip($tooltip)
            ->view($view);

        $badge === true
            ? $group->badge()
            : $group->badge($badge);

        if ($button) {
            $group
                ->button()
                ->iconPosition($attributes->get('iconPosition') ?? $attributes->get('icon-position'))
                ->outlined($attributes->get('outlined') ?? false);
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
    @php
        /**
         * Reset each child action's own bound record to match the group's current
         * record before checking visibility. See the longer explanation below (in the
         * dropdown branch) for why this is necessary.
         *
         * $group only has getRecord() when it's a table ActionGroup (via the
         * InteractsWithRecord trait) - this same view also renders page/form/infolist
         * action groups, which don't implement HasRecord at all, so this must be
         * guarded rather than called unconditionally.
         */
        if ($group instanceof \Filament\Actions\Contracts\HasRecord) {
            $currentRecord = $group->getRecord();

            foreach ($group->getActions() as $action) {
                if ($action instanceof \Filament\Actions\Contracts\HasRecord) {
                    $action->record($currentRecord);
                }
            }
        }
    @endphp

    @foreach ($group->getActions() as $action)
        @if ($action->isVisible())
            {{ $action }}
        @endif
    @endforeach
@else
    @php
        /**
         * When a table row action is clicked, Table::getAction() (see
         * Filament\Tables\Table\Concerns\HasActions::getAction()) does
         * `$action->record($mountedRecord)` directly on the SPECIFIC child action
         * being mounted - not only on the parent ActionGroup. Because that action's
         * record lookup (InteractsWithRecord::getRecord()) checks its OWN $record
         * property first and only falls back to the parent group's record if its own
         * is null, that action keeps using the clicked row's record for the REST of
         * the request - including when Livewire re-renders the whole table to show
         * the resulting confirmation modal. So for every other row rendered
         * afterwards, that one action evaluates its ->visible()/->hidden() closures
         * against the WRONG (originally-clicked) record, and can wrongly appear - or
         * wrongly make the whole group non-empty and therefore visible - on rows that
         * should show no actions at all.
         *
         * Fix: before evaluating any child action's visibility for this row, force its
         * record back to the group's current (correct, per-row) record. This runs on
         * every render, so it always overwrites whatever Table::getAction() may have
         * stuck onto an individual action earlier in the same request.
         *
         * $group only has getRecord() when it's a table ActionGroup (via the
         * InteractsWithRecord trait) - this same view also renders page/form/infolist
         * action groups, which don't implement HasRecord at all, so this must be
         * guarded rather than called unconditionally.
         */
        if ($group instanceof \Filament\Actions\Contracts\HasRecord) {
            $currentRecord = $group->getRecord();

            foreach ($group->getActions() as $action) {
                if ($action instanceof \Filament\Actions\Contracts\HasRecord) {
                    $action->record($currentRecord);
                }

                if ($action instanceof \Filament\Actions\ActionGroup) {
                    foreach ($action->getActions() as $nestedAction) {
                        if ($nestedAction instanceof \Filament\Actions\Contracts\HasRecord) {
                            $nestedAction->record($currentRecord);
                        }
                    }
                }
            }
        }

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
        :offset="$group->getDropdownOffset()"
        :placement="$group->getDropdownPlacement() ?? 'bottom-start'"
        :width="$group->getDropdownWidth()"
        teleport
    >
        <x-slot name="trigger">
            <x-dynamic-component
                :color="$group->getColor()"
                :component="$dynamicComponent"
                :icon="$group->getIcon()"
                :icon-size="$group->getIconSize()"
                :label-sr-only="$group->isLabelHidden()"
                :size="$group->getSize()"
                :tooltip="$group->getTooltip()"
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
