<?php

namespace Filament;

use Closure;
use Exception;
use Filament\Facades\Filament;
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
use Illuminate\Auth\EloquentUserProvider;
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

    protected string $authGuard = 'web';

    protected ?string $domain = null;

    protected string $globalSearchProvider = DefaultGlobalSearchProvider::class;

    protected string $homeUrl = '';

    protected bool $isNavigationMounted = false;

    protected ?string $login = null;

    protected array $navigationGroups = [];

    protected array $navigationItems = [];

    protected array $pages = [];

    protected ?string $pageDirectory = null;

    protected ?string $pageNamespace = null;

    protected array $resources = [];

    protected ?string $resourceDirectory = null;

    protected ?string $resourceNamespace = null;

    protected array $meta = [];

    protected string $path = '';

    protected ?string $tenantModel = null;

    protected ?string $tenantSlugField = null;

    protected string | Htmlable | null $theme = null;

    protected array $userMenuItems = [];

    protected array $widgets = [];

    protected ?string $widgetDirectory = null;

    protected ?string $widgetNamespace = null;

    protected ?Closure $navigationBuilder = null;

    protected array $renderHooks = [];

    public function auth(): Guard
    {
        return auth()->guard($this->authGuard);
    }

    public function authGuard(string $guard): static
    {
        $this->authGuard = $guard;

        return $this;
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

    public function domain(?string $domain = null): static
    {
        $this->domain = $domain;

        return $this;
    }

    public function globalSearchProvider(string $provider): static
    {
        if (! in_array(GlobalSearchProvider::class, class_implements($provider))) {
            throw new Exception("Global search provider {$provider} does not implement the " . GlobalSearchProvider::class . ' interface.');
        }

        $this->globalSearchProvider = $provider;

        return $this;
    }

    public function homeUrl(string $url): static
    {
        $this->homeUrl = $url;

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

    public function getAuthGuard(): string
    {
        return $this->authGuard;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
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

    public function hasRoutableTenancy(): bool
    {
        /** @var EloquentUserProvider $userProvider */
        $userProvider = $this->auth()->getProvider();

        return $this->hasTenancy() && ($userProvider->getModel() !== $this->getTenantModel());
    }

    public function hasTenancy(): bool
    {
        return filled($this->getTenantModel());
    }

    public function getGlobalSearchProvider(): GlobalSearchProvider
    {
        return app($this->globalSearchProvider);
    }

    public function getHomeUrl(): string
    {
        return $this->homeUrl;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTenant(string $key): Model
    {
        $tenantModel = $this->getTenantModel();

        $record = app($tenantModel)
            ->resolveRouteBinding($key, $this->getTenantSlugField());

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

    public function getLoginUrl(): ?string
    {
        if (! $this->login) {
            return null;
        }

        return route("filament.{$this->getId()}.auth.login");
    }

    public function getLogoutUrl(): string
    {
        return route("filament.{$this->getId()}.auth.logout");
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

    public function getUrl(?Model $tenant = null): ?string
    {
        if (! $this->auth()->check()) {
            return $this->hasLogin() ? $this->getLoginUrl() : url($this->getPath());
        }

        if ($tenant) {
            $originalTenant = Filament::getTenant();
            Filament::setTenant($tenant);

            $isNavigationMountedOriginally = $this->isNavigationMounted;
            $originalNavigationItems = $this->navigationItems;
            $originalNavigationGroups = $this->navigationGroups;

            $this->isNavigationMounted = false;
            $this->navigationItems = [];
            $this->navigationGroups = [];
        }

        $navigation = $this->getNavigation();

        if ($tenant) {
            Filament::setTenant($originalTenant);

            $this->isNavigationMounted = $isNavigationMountedOriginally;
            $this->navigationItems = $originalNavigationItems;
            $this->navigationGroups = $originalNavigationGroups;
        }

        $firstGroup = Arr::first($navigation);

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

    public function hasLogin(): bool
    {
        return filled($this->login);
    }

    public function discoverPages(string $in, string $for): static
    {
        $this->pageDirectory = $in;
        $this->pageNamespace = $for;

        $this->discoverComponents(
            Page::class,
            $this->pages,
            directory: $in,
            namespace: $for,
        );

        return $this;
    }

    public function getPageDirectory(): ?string
    {
        return $this->pageDirectory;
    }

    public function getPageNamespace(): ?string
    {
        return $this->pageNamespace;
    }

    public function discoverResources(string $in, string $for): static
    {
        $this->resourceDirectory = $in;
        $this->resourceNamespace = $for;

        $this->discoverComponents(
            Resource::class,
            $this->resources,
            directory: $in,
            namespace: $for,
        );

        return $this;
    }

    public function getResourceDirectory(): ?string
    {
        return $this->resourceDirectory;
    }

    public function getResourceNamespace(): ?string
    {
        return $this->resourceNamespace;
    }

    public function discoverWidgets(string $in, string $for): static
    {
        $this->widgetDirectory = $in;
        $this->widgetNamespace = $for;

        $this->discoverComponents(
            Widget::class,
            $this->widgets,
            directory: $in,
            namespace: $for,
        );

        return $this;
    }

    public function getWidgetDirectory(): ?string
    {
        return $this->widgetDirectory;
    }

    public function getWidgetNamespace(): ?string
    {
        return $this->widgetNamespace;
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
            ($this->hasLogin() ? [$this->getLogin()] : []),
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
