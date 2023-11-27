<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\Action;
use Filament\Tables\Grouping\Group;

trait CanGroupRecords
{
    protected string | Group | null $defaultGroup = null;

    /**
     * @var array<string, Group>
     */
    protected array $groups = [];

    protected bool | Closure $isGroupsOnly = false;

    protected bool | Closure $areGroupingSettingsInDropdownOnDesktop = false;

    protected bool | Closure $areGroupingSettingsHidden = false;

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

    public function defaultGroup(string | Group | null $group): static
    {
        $this->defaultGroup = $group;

        return $this;
    }

    /**
     * @param  array<Group | string>  $groups
     */
    public function groups(array $groups): static
    {
        foreach ($groups as $group) {
            if (! $group instanceof Group) {
                $group = Group::make($group);
            }

            $this->groups[$group->getId()] = $group;
        }

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
            ->icon(FilamentIcon::resolve('tables::actions.group') ?? 'heroicon-m-rectangle-stack')
            ->color('gray')
            ->livewireClickHandlerEnabled(false)
            ->table($this);

        if ($this->modifyGroupRecordsTriggerActionUsing) {
            $action = $this->evaluate($this->modifyGroupRecordsTriggerActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        if ($action->getView() === Action::BUTTON_VIEW) {
            $action->defaultSize(ActionSize::Small);
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

    public function getDefaultGroup(): ?Group
    {
        if ($this->defaultGroup === null) {
            return null;
        }

        if ($this->defaultGroup instanceof Group) {
            return $this->defaultGroup;
        }

        $group = $this->getGroup($this->defaultGroup);

        if ($group) {
            return $group;
        }

        return Group::make($this->defaultGroup);
    }

    /**
     * @return array<string, Group>
     */
    public function getGroups(): array
    {
        return $this->groups;
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
