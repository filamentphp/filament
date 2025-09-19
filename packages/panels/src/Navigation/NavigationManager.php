<?php

namespace Filament\Navigation;

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use LogicException;
use UnitEnum;

class NavigationManager
{
    protected Panel $panel;

    protected bool $isNavigationMounted = false;

    /**
     * @var array<string | int, NavigationGroup | string>
     */
    protected array $navigationGroups = [];

    /**
     * @var array<NavigationItem>
     */
    protected array $navigationItems = [];

    public function __construct()
    {
        $this->panel = Filament::getCurrentOrDefaultPanel();

        $this->navigationGroups = array_map(
            fn (NavigationGroup | string $group): NavigationGroup | string => $group instanceof NavigationGroup ? (clone $group) : $group,
            $this->panel->getNavigationGroups(),
        );
        $this->navigationItems = array_map(
            fn (NavigationItem $item): NavigationItem => clone $item,
            $this->panel->getNavigationItems(),
        );
    }

    /**
     * @return array<NavigationGroup>
     */
    public function get(): array
    {
        if ($this->panel->hasNavigationBuilder()) {
            return $this->panel->buildNavigation();
        }

        if (! $this->isNavigationMounted) {
            $this->mountNavigation();
        }

        $groups = collect($this->getNavigationGroups());

        return collect($this->getNavigationItems())
            ->filter(fn (NavigationItem $item): bool => $item->isVisible())
            ->sortBy(fn (NavigationItem $item): int => $item->getSort())
            ->groupBy(function (NavigationItem $item): string {
                $group = $item->getGroup();

                return serialize($group);
            })
            ->map(function (Collection $items, string $groupIndex) use ($groups): NavigationGroup {
                $parentItems = $items->groupBy(fn (NavigationItem $item): string => $item->getParentItem() ?? '');

                $items = $parentItems->get('', collect())
                    ->keyBy(fn (NavigationItem $item): string => $item->getLabel());

                $parentItems->except([''])->each(function (Collection $parentItemItems, string $parentItemLabel) use ($items): void {
                    if (! $items->has($parentItemLabel)) {
                        return;
                    }

                    $items->get($parentItemLabel)->childItems($parentItemItems);
                });

                $items = $items->filter(fn (NavigationItem $item): bool => (filled($item->getChildItems()) || filled($item->getUrl())));

                $groupName = unserialize($groupIndex);

                if (blank($groupName)) {
                    return NavigationGroup::make()->items($items);
                }

                $groupEnum = null;

                if ($groupName instanceof UnitEnum) {
                    $groupEnum = $groupName;
                    $groupName = $groupEnum->name;
                }

                $registeredGroup = $groups
                    ->first(function (NavigationGroup | string $registeredGroup, string | int $registeredGroupIndex) use ($groupName) {
                        if ($registeredGroupIndex === $groupName) {
                            return true;
                        }

                        if ($registeredGroup === $groupName) {
                            return true;
                        }

                        if (! $registeredGroup instanceof NavigationGroup) {
                            return false;
                        }

                        return $registeredGroup->getLabel() === $groupName;
                    });

                if ($registeredGroup instanceof NavigationGroup) {
                    return $registeredGroup->items($items);
                }

                $group = NavigationGroup::make($registeredGroup ?? $groupName);

                if ($groupEnum instanceof HasLabel) {
                    $group->label($groupEnum->getLabel());
                }

                if ($groupEnum instanceof HasIcon) {
                    $group->icon($groupEnum->getIcon());
                }

                return $group->items($items);
            })
            ->filter(fn (NavigationGroup $group): bool => filled($group->getItems()))
            ->sortBy(function (NavigationGroup $group, ?string $groupIndex): int {
                if (blank($group->getLabel())) {
                    return -1;
                }

                $groupName = unserialize($groupIndex);
                $groupEnum = null;

                if ($groupName instanceof UnitEnum) {
                    $groupEnum = $groupName;
                    $groupName = $groupEnum->name;
                }

                $registeredGroups = $this->getNavigationGroups();

                $groupsToSearch = $registeredGroups;

                if (Arr::first($registeredGroups) instanceof NavigationGroup) {
                    $groupsToSearch = [
                        ...array_keys($registeredGroups),
                        ...array_map(fn (NavigationGroup $registeredGroup): string => $registeredGroup->getLabel(), array_values($registeredGroups)),
                    ];
                }

                $sort = array_search(
                    $groupName,
                    $groupsToSearch,
                );

                if ($groupEnum) {
                    $enumCaseSort = array_search($groupEnum, $groupEnum::cases());
                    $sort = ($enumCaseSort !== false) ? $enumCaseSort : $sort;
                }

                if ($sort === false) {
                    return count($registeredGroups);
                }

                return $sort;
            })
            ->all();
    }

    public function mountNavigation(): void
    {
        foreach ($this->panel->getPages() as $page) {
            $page::registerNavigationItems();
        }

        foreach ($this->panel->getResources() as $resource) {
            $resource::registerNavigationItems();
        }

        $this->isNavigationMounted = true;
    }

    /**
     * @param  array<string | int, NavigationGroup | string> | class-string<UnitEnum>  $groups
     */
    public function navigationGroups(array | string $groups): static
    {
        if (is_string($groups)) {
            throw_unless(enum_exists($groups), new LogicException("Enum class [{$groups}] does not exist for navigation groups."));

            $groups = array_reduce(
                $groups::cases(),
                function (array $carry, UnitEnum $case): array {
                    $carry[$case->name] = NavigationGroup::fromEnum($case);

                    return $carry;
                },
                initial: [],
            );
        }

        $this->navigationGroups = [
            ...$this->navigationGroups,
            ...$groups,
        ];

        return $this;
    }

    /**
     * @param  array<NavigationItem>  $items
     */
    public function navigationItems(array $items): static
    {
        $this->navigationItems = [
            ...$this->navigationItems,
            ...$items,
        ];

        return $this;
    }

    /**
     * @return array<string | int, NavigationGroup | string>
     */
    public function getNavigationGroups(): array
    {
        return $this->navigationGroups;
    }

    /**
     * @return array<NavigationItem>
     */
    public function getNavigationItems(): array
    {
        return $this->navigationItems;
    }
}
