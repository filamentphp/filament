<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Grouping\Group;

trait CanGroupRecords
{
    protected string | Group | null $defaultGroup = null;

    /**
     * @var array<string, Group>
     */
    protected array $groups = [];

    protected bool | Closure $isGroupsOnly = false;

    public function defaultGroup(string | Group | null $group): static
    {
        $this->defaultGroup = $group;

        return $this;
    }

    /**
     * @param  array<Group>  $groups
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

    public function isDefaultGroupSelectable(): bool
    {
        $defaultGroup = $this->getDefaultGroup();

        if (! $defaultGroup) {
            return false;
        }

        return $this->getGroup($defaultGroup->getId()) !== null;
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
        return $this->isGroupsOnly;
    }


}
