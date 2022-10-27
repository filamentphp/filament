<?php

namespace Filament;

use Closure;
use Exception;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Filament\GlobalSearch\DefaultGlobalSearchProvider;
use Filament\Http\Livewire\Auth\Login;
use Filament\Http\Livewire\GlobalSearch;
use Filament\Http\Livewire\Notifications;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\UserMenuItem;
use Filament\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Livewire\Livewire;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class Context
{
    protected string $id;

    protected string $globalSearchProvider = DefaultGlobalSearchProvider::class;

    protected bool $isNavigationMounted = false;

    protected ?string $login = null;

    protected array $navigationGroups = [];

    protected array $navigationItems = [];

    protected array $pages = [];

    protected array $resources = [];

    protected array $meta = [];

    protected string $path = '';

    protected ?string $tenantModel = null;

    protected ?string $tenantSlugField = null;

    protected string | Htmlable | null $theme = null;

    protected array $userMenuItems = [];

    protected array $widgets = [];

    protected ?Closure $navigationBuilder = null;

    protected array $renderHooks = [];

    public function auth(): Guard
    {
        return auth()->guard(config('filament.auth.guard'));
    }

    public function navigation(Closure $builder): static
    {
        $this->navigationBuilder = $builder;

        return $this;
    }

    public function boot(): void
    {
        $this->registerLivewireComponents();
    }

    public function buildNavigation(): array
    {
        /** @var \Filament\Navigation\NavigationBuilder $builder */
        $builder = app()->call($this->navigationBuilder);

        return $builder->getNavigation();
    }

    public function globalSearchProvider(string $provider): static
    {
        if (! in_array(GlobalSearchProvider::class, class_implements($provider))) {
            throw new Exception("Global search provider {$provider} does not implement the " . GlobalSearchProvider::class . ' interface.');
        }

        $this->globalSearchProvider = $provider;

        return $this;
    }

    public function id(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function login(?string $component = Login::class): static
    {
        $this->login = $component;

        return $this;
    }

    public function mountNavigation(): void
    {
        foreach ($this->getPages() as $page) {
            $page::registerNavigationItems();
        }

        foreach ($this->getResources() as $resource) {
            $resource::registerNavigationItems();
        }

        $this->isNavigationMounted = true;
    }

    public function navigationGroups(array $groups): static
    {
        $this->navigationGroups = array_merge($this->navigationGroups, $groups);

        return $this;
    }

    public function navigationItems(array $items): static
    {
        $this->navigationItems = array_merge($this->navigationItems, $items);

        return $this;
    }

    public function pages(array $pages): static
    {
        $this->pages = array_merge($this->pages, $pages);

        return $this;
    }

    public function path(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function renderHook(string $name, Closure $callback): static
    {
        $this->renderHooks[$name][] = $callback;

        return $this;
    }

    public function resources(array $resources): static
    {
        $this->resources = array_merge($this->resources, $resources);

        return $this;
    }

    public function theme(string | Htmlable | null $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function userMenuItems(array $items): static
    {
        $this->userMenuItems = array_merge($this->userMenuItems, $items);

        return $this;
    }

    public function widgets(array $widgets): static
    {
        $this->widgets = array_merge($this->widgets, $widgets);

        return $this;
    }

    public function meta(array $meta): static
    {
        $this->meta = array_merge($this->meta, $meta);

        return $this;
    }

    public function getRenderHook(string $name): Htmlable
    {
        $hooks = array_map(
            fn (callable $hook): string => (string) app()->call($hook),
            $this->renderHooks[$name] ?? [],
        );

        return new HtmlString(implode('', $hooks));
    }

    public function tenant(?string $model, ?string $slugField = null): static
    {
        $this->tenantModel = $model;
        $this->tenantSlugField = $slugField;

        return $this;
    }

    public function hasTenancy(): bool
    {
        return filled($this->getTenantModel());
    }

    public function getGlobalSearchProvider(): GlobalSearchProvider
    {
        return app($this->globalSearchProvider);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTenant(string $key): Model
    {
        $tenantModel = $this->getTenantModel();

        $record = app($tenantModel)
            ->resolveRouteBinding($key, $this->getTenantSlugField())
            ->first();

        if ($record === null) {
            throw (new ModelNotFoundException())->setModel($tenantModel, [$key]);
        }

        return $record;
    }

    public function getTenantModel(): ?string
    {
        return $this->tenantModel;
    }

    public function getTenantSlugField(): ?string
    {
        return $this->tenantSlugField;
    }

    public function getNavigation(): array
    {
        if ($this->navigationBuilder !== null) {
            return $this->buildNavigation();
        }

        if (! $this->isNavigationMounted) {
            $this->mountNavigation();
        }

        return collect($this->getNavigationItems())
            ->sortBy(fn (Navigation\NavigationItem $item): int => $item->getSort())
            ->groupBy(fn (Navigation\NavigationItem $item): ?string => $item->getGroup())
            ->map(function (Collection $items, ?string $groupIndex): NavigationGroup {
                if (blank($groupIndex)) {
                    return NavigationGroup::make()->items($items);
                }

                $registeredGroup = collect($this->getNavigationGroups())
                    ->first(function (NavigationGroup | string $registeredGroup, string | int $registeredGroupIndex) use ($groupIndex) {
                        if ($registeredGroupIndex === $groupIndex) {
                            return true;
                        }

                        if ($registeredGroup === $groupIndex) {
                            return true;
                        }

                        if (! $registeredGroup instanceof NavigationGroup) {
                            return false;
                        }

                        return $registeredGroup->getLabel() === $groupIndex;
                    });

                if ($registeredGroup instanceof NavigationGroup) {
                    return $registeredGroup->items($items);
                }

                return NavigationGroup::make($registeredGroup ?? $groupIndex)
                    ->items($items);
            })
            ->sortBy(function (NavigationGroup $group, ?string $groupIndex): int {
                if (blank($group->getLabel())) {
                    return -1;
                }

                $registeredGroups = $this->getNavigationGroups();

                $groupsToSearch = $registeredGroups;

                if (Arr::first($registeredGroups) instanceof NavigationGroup) {
                    $groupsToSearch = array_merge(
                        array_keys($registeredGroups),
                        array_map(fn (NavigationGroup $registeredGroup): string => $registeredGroup->getLabel(), array_values($registeredGroups)),
                    );
                }

                $sort = array_search(
                    $groupIndex,
                    $groupsToSearch,
                );

                if ($sort === false) {
                    return count($registeredGroups);
                }

                return $sort;
            })
            ->all();
    }

    public function getNavigationGroups(): array
    {
        return $this->navigationGroups;
    }

    public function getNavigationItems(): array
    {
        return $this->navigationItems;
    }

    public function getPages(): array
    {
        return array_unique($this->pages);
    }

    public function getResources(): array
    {
        return array_unique($this->resources);
    }

    public function getUserMenuItems(): array
    {
        return collect($this->userMenuItems)
            ->sort(fn (UserMenuItem $item): int => $item->getSort())
            ->all();
    }

    public function getModelResource(string | Model $model): ?string
    {
        if ($model instanceof Model) {
            $model = $model::class;
        }

        foreach ($this->getResources() as $resource) {
            if ($model !== $resource::getModel()) {
                continue;
            }

            return $resource;
        }

        return null;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getTheme(): string | Htmlable | null
    {
        return $this->theme;
    }

    public function getUrl(): ?string
    {
        $firstGroup = Arr::first($this->getNavigation());

        if (! $firstGroup) {
            return null;
        }

        $firstItem = Arr::first($firstGroup->getItems());

        if (! $firstItem) {
            return null;
        }

        return $firstItem->getUrl();
    }

    public function getWidgets(): array
    {
        return collect($this->widgets)
            ->unique()
            ->sortBy(fn (string $widget): int => $widget::getSort())
            ->all();
    }

    public function getMeta(): array
    {
        return array_unique($this->meta);
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function discoverPages(string $in, string $for): static
    {
        $this->discoverComponents(
            Page::class,
            $this->pages,
            directory: $in,
            namespace: $for,
        );

        return $this;
    }

    public function discoverResources(string $in, string $for): static
    {
        $this->discoverComponents(
            Resource::class,
            $this->resources,
            directory: $in,
            namespace: $for,
        );

        return $this;
    }

    public function discoverWidgets(string $in, string $for): static
    {
        $this->discoverComponents(
            Widget::class,
            $this->widgets,
            directory: $in,
            namespace: $for,
        );

        return $this;
    }

    protected function discoverComponents(string $baseClass, array &$register, ?string $directory, ?string $namespace): void
    {
        if (blank($directory) || blank($namespace)) {
            return;
        }

        $filesystem = app(Filesystem::class);

        if ((! $filesystem->exists($directory)) && (! str($directory)->contains('*'))) {
            return;
        }

        $namespace = str($namespace);

        $register = array_merge(
            $register,
            collect($filesystem->allFiles($directory))
                ->map(function (SplFileInfo $file) use ($namespace): string {
                    $variableNamespace = $namespace->contains('*') ? str_ireplace(
                        ['\\' . $namespace->before('*'), $namespace->after('*')],
                        ['', ''],
                        str($file->getPath())
                            ->after(base_path())
                            ->replace(['/'], ['\\']),
                    ) : null;

                    return (string) $namespace
                        ->append('\\', $file->getRelativePathname())
                        ->replace('*', $variableNamespace)
                        ->replace(['/', '.php'], ['\\', '']);
                })
                ->filter(fn (string $class): bool => is_subclass_of($class, $baseClass) && (! (new ReflectionClass($class))->isAbstract()))
                ->all(),
        );
    }

    public function registerLivewireComponents(): void
    {
        $components = array_merge(
            (($login = $this->getLogin()) ? [$login] : []),
            [
                GlobalSearch::class,
                Notifications::class,
            ],
            $this->getPages(),
            $this->getWidgets(),
        );

        foreach ($this->getResources() as $resource) {
            foreach ($resource::getPages() as $page) {
                $components[] = $page['class'];
            }

            foreach ($resource::getRelations() as $relation) {
                if ($relation instanceof RelationGroup) {
                    foreach ($relation->getManagers() as $groupedRelation) {
                        $components[] = $groupedRelation;
                    }

                    continue;
                }

                $components[] = $relation;
            }

            foreach ($resource::getWidgets() as $widget) {
                $components[] = $widget;
            }
        }

        foreach ($components as $component) {
            Livewire::component($component::getName(), $component);
        }
    }
}
