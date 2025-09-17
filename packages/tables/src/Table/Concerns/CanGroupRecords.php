<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Support\Enums\Size;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Grouping\Group;
use Filament\Tables\View\TablesIconAlias;

trait CanGroupRecords
{
    protected string | Group | Closure | null $defaultGroup = null;

    /**
     * @var array<string, Group>
     */
    protected ?array $cachedGroups;

    /**
     * @var array<string | Group> | Closure
     */
    protected array | Closure $groups = [];

    protected bool | Closure $isGroupsOnly = false;

    protected bool | Closure $areGroupingSettingsInDropdownOnDesktop = false;

    protected bool | Closure $areGroupingSettingsHidden = false;

    protected bool | Closure $isGroupingDirectionSettingHidden = false;

    protected ?Closure $modifyGroupRecordsTriggerActionUsing = null;

    public function groupRecordsTriggerAction(?Closure $callback): static
    {
        $this->modifyGroupRecordsTriggerActionUsing = $callback;

        return $this;
    }

    public function groupingSettingsInDropdownOnDesktop(bool | Closure $condition = true): static
    {
        $this->areGroupingSettingsInDropdownOnDesktop = $condition;

        return $this;
    }

    /**
     * @deprecated Use the `groupingSettingsInDropdownOnDesktop()` method instead.
     */
    public function groupsInDropdownOnDesktop(bool | Closure $condition = true): static
    {
        $this->groupingSettingsInDropdownOnDesktop($condition);

        return $this;
    }

    public function groupingSettingsHidden(bool | Closure $condition = true): static
    {
        $this->areGroupingSettingsHidden = $condition;

        return $this;
    }

    public function groupingDirectionSettingHidden(bool | Closure $condition = true): static
    {
        $this->isGroupingDirectionSettingHidden = $condition;

        return $this;
    }

    public function defaultGroup(string | Group | Closure | null $group): static
    {
        $this->defaultGroup = $group;

        return $this;
    }

    /**
     * @param  array<string | Group> | Closure  $groups
     */
    public function groups(array | Closure $groups): static
    {
        $this->groups = $groups;

        return $this;
    }

    public function groupsOnly(bool | Closure $condition = true): static
    {
        $this->isGroupsOnly = $condition;

        return $this;
    }

    public function getGroupRecordsTriggerAction(): Action
    {
        $action = Action::make('groupRecords')
            ->label(__('filament-tables::table.actions.group.label'))
            ->iconButton()
            ->icon(FilamentIcon::resolve(TablesIconAlias::ACTIONS_GROUP) ?? Heroicon::RectangleStack)
            ->color('gray')
            ->livewireClickHandlerEnabled(false)
            ->table($this)
            ->authorize(true);

        if ($this->modifyGroupRecordsTriggerActionUsing) {
            $action = $this->evaluate($this->modifyGroupRecordsTriggerActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        if ($action->getView() === Action::BUTTON_VIEW) {
            $action->defaultSize(Size::Small);
        }

        return $action;
    }

    public function isDefaultGroupSelectable(): bool
    {
        $defaultGroup = $this->getDefaultGroup();

        if (! $defaultGroup) {
            return false;
        }

        return $this->getGroup($defaultGroup->getId()) !== null;
    }

    public function areGroupingSettingsInDropdownOnDesktop(): bool
    {
        return (bool) $this->evaluate($this->areGroupingSettingsInDropdownOnDesktop);
    }

    public function areGroupingSettingsHidden(): bool
    {
        return (bool) $this->evaluate($this->areGroupingSettingsHidden);
    }

    public function isGroupingDirectionSettingHidden(): bool
    {
        return (bool) $this->evaluate($this->isGroupingDirectionSettingHidden);
    }

    public function getDefaultGroup(): ?Group
    {
        $defaultGroup = $this->evaluate($this->defaultGroup);

        if ($defaultGroup === null) {
            return null;
        }

        if ($defaultGroup instanceof Group) {
            return $defaultGroup->table($this);
        }

        $group = $this->getGroup($defaultGroup);

        if ($group) {
            return $group;
        }

        return Group::make($defaultGroup)
            ->table($this);
    }

    /**
     * @return array<string, Group>
     */
    public function getGroups(): array
    {
        return $this->cachedGroups ??= array_reduce(
            $this->evaluate($this->groups),
            function (array $carry, $group): array {
                if (! $group instanceof Group) {
                    $group = Group::make($group);
                }

                $carry[$group->getId()] = $group->table($this);

                return $carry;
            },
            initial: [],
        );
    }

    public function getGroup(string $id): ?Group
    {
        return $this->getGroups()[$id] ?? null;
    }

    public function getGrouping(): ?Group
    {
        return $this->getLivewire()->getTableGrouping();
    }

    public function isGroupsOnly(): bool
    {
        return (bool) $this->evaluate($this->isGroupsOnly);
    }
}
