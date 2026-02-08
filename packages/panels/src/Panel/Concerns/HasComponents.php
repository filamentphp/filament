<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Auth\Pages\EditProfile;
use Filament\Clusters\Cluster;
use Filament\Livewire\GlobalSearch;
use Filament\Livewire\Notifications;
use Filament\Livewire\SimpleUserMenu;
use Filament\Pages\Page;
use Filament\Resources\Pages\Page as ResourcePage;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Resources\Resource;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\Mechanisms\ComponentRegistry;
use ReflectionClass;

trait HasComponents
{
    /**
     * @var array<string, class-string>
     */
    protected array $livewireComponents = [];

    /**
     * @var array<class-string>
     */
    protected array $pages = [];

    /**
     * @var array<string>
     */
    protected array $pageDirectories = [];

    /**
     * @var array<string>
     */
    protected array $pageNamespaces = [];

    /**
     * @var array<class-string<Cluster>>
     */
    protected array $clusters = [];

    /**
     * @var array<string>
     */
    protected array $clusterDirectories = [];

    /**
     * @var array<string>
     */
    protected array $clusterNamespaces = [];

    /**
     * @var array<class-string<Cluster>, array<class-string>>
     */
    protected array $clusteredComponents = [];

    /**
     * @var array<class-string>
     */
    protected array $resources = [];

    /**
     * @var array<string>
     */
    protected array $resourceDirectories = [];

    /**
     * @var array<string>
     */
    protected array $resourceNamespaces = [];

    /**
     * @var array<class-string<Widget>>
     */
    protected array $widgets = [];

    /**
     * @var array<string>
     */
    protected array $widgetDirectories = [];

    /**
     * @var array<string>
     */
    protected array $widgetNamespaces = [];

    protected bool | Closure $hasReadOnlyRelationManagersOnResourceViewPagesByDefault = true;

    protected ?bool $hasCachedComponents = null;

    protected string | Closure | null $resourceCreatePageRedirect = null;

    protected string | Closure | null $resourceEditPageRedirect = null;

    /**
     * @param  array<class-string>  $pages
     */
    public function pages(array $pages): static
    {
        if ($this->hasCachedComponents()) {
            return $this;
        }

        $this->pages = [
            ...$this->pages,
            ...$pages,
        ];

        foreach ($pages as $page) {
            $this->queueLivewireComponentForRegistration($page);
            $this->registerToCluster($page);
        }

        return $this;
    }

    /**
     * @param  array<class-string>  $resources
     */
    public function resources(array $resources): static
    {
        if ($this->hasCachedComponents()) {
            return $this;
        }

        $this->resources = [
            ...$this->resources,
            ...$resources,
        ];

        foreach ($resources as $resource) {
            $this->registerToCluster($resource);
        }

        return $this;
    }

    /**
     * @param  class-string<Model>|Model  $model
     */
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

    /**
     * @param  array<class-string<Widget> | WidgetConfiguration>  $widgets
     */
    public function widgets(array $widgets): static
    {
        if ($this->hasCachedComponents()) {
            return $this;
        }

        $this->widgets = [
            ...$this->widgets,
            ...$widgets,
        ];

        foreach ($widgets as $widget) {
            $this->queueLivewireComponentForRegistration($this->normalizeWidgetClass($widget));
        }

        return $this;
    }

    /**
     * @param  class-string<Widget> | WidgetConfiguration  $widget
     * @return class-string<Widget>
     */
    protected function normalizeWidgetClass(string | WidgetConfiguration $widget): string
    {
        if ($widget instanceof WidgetConfiguration) {
            return $widget->widget;
        }

        return $widget;
    }

    public function discoverPages(string $in, string $for): static
    {
        if ($this->hasCachedComponents()) {
            return $this;
        }

        $this->pageDirectories[] = $in;
        $this->pageNamespaces[] = $for;

        $this->discoverComponents(
            Page::class,
            $this->pages,
            directory: $in,
            namespace: $for,
        );

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getPageDirectories(): array
    {
        return $this->pageDirectories;
    }

    /**
     * @return array<string>
     */
    public function getPageNamespaces(): array
    {
        return $this->pageNamespaces;
    }

    public function discoverClusters(string $in, string $for): static
    {
        if ($this->hasCachedComponents()) {
            return $this;
        }

        $this->clusterDirectories[] = $in;
        $this->clusterNamespaces[] = $for;

        $this->discoverComponents(
            Cluster::class,
            $this->clusters, /** @phpstan-ignore assign.propertyType */
            directory: $in,
            namespace: $for,
        );
        $this->discoverComponents(
            Page::class,
            $this->pages,
            directory: $in,
            namespace: $for,
        );
        $this->discoverComponents(
            Resource::class,
            $this->resources,
            directory: $in,
            namespace: $for,
        );

        return $this;
    }

    /**
     * @return array<class-string<Cluster>>
     */
    public function getClusters(): array
    {
        return $this->clusters;
    }

    /**
     * @return array<string>
     */
    public function getClusterDirectories(): array
    {
        return $this->clusterDirectories;
    }

    /**
     * @return array<string>
     */
    public function getClusterNamespaces(): array
    {
        return $this->clusterNamespaces;
    }

    public function discoverResources(string $in, string $for): static
    {
        if ($this->hasCachedComponents()) {
            return $this;
        }

        $this->resourceDirectories[] = $in;
        $this->resourceNamespaces[] = $for;

        $this->discoverComponents(
            Resource::class,
            $this->resources,
            directory: $in,
            namespace: $for,
        );

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getResourceDirectories(): array
    {
        return $this->resourceDirectories;
    }

    /**
     * @return array<string>
     */
    public function getResourceNamespaces(): array
    {
        return $this->resourceNamespaces;
    }

    public function discoverWidgets(string $in, string $for): static
    {
        if ($this->hasCachedComponents()) {
            return $this;
        }

        $this->widgetDirectories[] = $in;
        $this->widgetNamespaces[] = $for;

        $this->discoverComponents(
            Widget::class,
            $this->widgets, /** @phpstan-ignore assign.propertyType */
            directory: $in,
            namespace: $for,
        );

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getWidgetDirectories(): array
    {
        return $this->widgetDirectories;
    }

    /**
     * @return array<string>
     */
    public function getWidgetNamespaces(): array
    {
        return $this->widgetNamespaces;
    }

    public function discoverLivewireComponents(string $in, string $for): static
    {
        if ($this->hasCachedComponents()) {
            return $this;
        }

        $components = [];

        $this->discoverComponents(
            Component::class,
            $components,
            directory: $in,
            namespace: $for,
        );

        return $this;
    }

    /**
     * @return array<class-string>
     */
    public function getPages(): array
    {
        return array_unique($this->pages);
    }

    /**
     * @return array<class-string>
     */
    public function getResources(): array
    {
        return array_unique($this->resources);
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return collect($this->widgets)
            ->unique()
            ->sortBy(fn (string | WidgetConfiguration $widget): int => $this->normalizeWidgetClass($widget)::getSort())
            ->all();
    }

    /**
     * @param  array<string, class-string<Component>>  $register
     */
    public function discoverComponents(string $baseClass, array &$register, ?string $directory, ?string $namespace): void
    {
        if (blank($directory) || blank($namespace)) {
            return;
        }

        $filesystem = app(Filesystem::class);

        if ((! $filesystem->exists($directory)) && (! str($directory)->contains('*'))) {
            return;
        }

        $namespace = str($namespace);

        foreach ($filesystem->allFiles($directory) as $file) {
            $variableNamespace = $namespace->contains('*') ? str_ireplace(
                ['\\' . $namespace->before('*'), $namespace->after('*')],
                ['', ''],
                str_replace([DIRECTORY_SEPARATOR], ['\\'], (string) str($file->getPath())->after(base_path())),
            ) : null;

            if (is_string($variableNamespace)) {
                $variableNamespace = (string) str($variableNamespace)->before('\\');
            }

            $class = (string) $namespace
                ->append('\\', $file->getRelativePathname())
                ->replace('*', $variableNamespace ?? '')
                ->replace([DIRECTORY_SEPARATOR, '.php'], ['\\', '']);

            if (! class_exists($class)) {
                continue;
            }

            if ((new ReflectionClass($class))->isAbstract()) {
                continue;
            }

            if (is_subclass_of($class, Component::class)) {
                $this->queueLivewireComponentForRegistration($class);
            }

            if (! is_subclass_of($class, $baseClass)) { /** @phpstan-ignore function.alreadyNarrowedType */
                continue;
            }

            if (
                method_exists($class, 'isDiscovered') &&
                (! $class::isDiscovered())
            ) {
                continue;
            }

            $register[$file->getRealPath()] = $class; /** @phpstan-ignore parameterByRef.type */
            $this->registerToCluster($class);
        }
    }

    /**
     * @param  array<class-string<Component>>  $components
     */
    public function livewireComponents(array $components): static
    {
        if ($this->hasCachedComponents()) {
            return $this;
        }

        foreach ($components as $component) {
            $this->queueLivewireComponentForRegistration($component);
        }

        return $this;
    }

    protected function registerLivewireComponents(): void
    {
        if (! $this->hasCachedComponents()) {
            $this->queueLivewireComponentForRegistration($this->getDatabaseNotificationsLivewireComponent());
            $this->queueLivewireComponentForRegistration(EditProfile::class);
            $this->queueLivewireComponentForRegistration(GlobalSearch::class);
            $this->queueLivewireComponentForRegistration(Notifications::class);
            $this->queueLivewireComponentForRegistration($this->getSidebarLivewireComponent());
            $this->queueLivewireComponentForRegistration(SimpleUserMenu::class);
            $this->queueLivewireComponentForRegistration($this->getTopbarLivewireComponent());

            if ($this->hasEmailVerification() && is_subclass_of($emailVerificationPromptRouteAction = $this->getEmailVerificationPromptRouteAction(), Component::class)) {
                $this->queueLivewireComponentForRegistration($emailVerificationPromptRouteAction);
            }

            if ($this->hasLogin() && is_subclass_of($loginRouteAction = $this->getLoginRouteAction(), Component::class)) {
                $this->queueLivewireComponentForRegistration($loginRouteAction);
            }

            if ($this->isMultiFactorAuthenticationRequired() && is_subclass_of($setUpRequiredMultiFactorAuthenticationRouteAction = $this->getSetUpRequiredMultiFactorAuthenticationRouteAction(), Component::class)) {
                $this->queueLivewireComponentForRegistration($setUpRequiredMultiFactorAuthenticationRouteAction);
            }

            if ($this->hasPasswordReset()) {
                if (is_subclass_of($requestPasswordResetRouteAction = $this->getRequestPasswordResetRouteAction(), Component::class)) {
                    $this->queueLivewireComponentForRegistration($requestPasswordResetRouteAction);
                }

                if (is_subclass_of($resetPasswordRouteAction = $this->getResetPasswordRouteAction(), Component::class)) {
                    $this->queueLivewireComponentForRegistration($resetPasswordRouteAction);
                }
            }

            if ($this->hasProfile() && is_subclass_of($profilePageComponent = $this->getProfilePage(), Component::class)) {
                $this->queueLivewireComponentForRegistration($profilePageComponent);
            }

            if ($this->hasRegistration() && is_subclass_of($registrationRouteAction = $this->getRegistrationRouteAction(), Component::class)) {
                $this->queueLivewireComponentForRegistration($registrationRouteAction);
            }

            if ($this->hasTenantRegistration() && is_subclass_of($tenantRegistrationComponent = $this->getTenantRegistrationPage(), Component::class)) {
                $this->queueLivewireComponentForRegistration($tenantRegistrationComponent);
            }

            if ($this->hasTenantProfile() && is_subclass_of($tenantProfileComponent = $this->getTenantProfilePage(), Component::class)) {
                $this->queueLivewireComponentForRegistration($tenantProfileComponent);
            }

            foreach ($this->getResources() as $resource) {
                foreach ($resource::getPages() as $pageRegistration) {
                    $this->queueLivewireComponentForRegistration($pageRegistration->getPage());
                }

                foreach ($resource::getRelations() as $relation) {
                    if ($relation instanceof RelationGroup) {
                        foreach ($relation->getManagers() as $groupedRelation) {
                            $this->queueLivewireComponentForRegistration($this->normalizeRelationManagerClass($groupedRelation));
                        }

                        continue;
                    }

                    $this->queueLivewireComponentForRegistration($this->normalizeRelationManagerClass($relation));
                }

                foreach ($resource::getWidgets() as $widget) {
                    $this->queueLivewireComponentForRegistration($this->normalizeWidgetClass($widget));
                }
            }
        }

        foreach ($this->livewireComponents as $componentName => $componentClass) {
            Livewire::component($componentName, $componentClass);
        }
    }

    /**
     * @param  class-string<RelationManager> | RelationManagerConfiguration  $manager
     * @return class-string<RelationManager>
     */
    protected function normalizeRelationManagerClass(string | RelationManagerConfiguration $manager): string
    {
        if ($manager instanceof RelationManagerConfiguration) {
            return $manager->relationManager;
        }

        return $manager;
    }

    protected function registerToCluster(string $component): void
    {
        if (! method_exists($component, 'getCluster')) {
            return;
        }

        if (is_subclass_of($component, ResourcePage::class)) {
            return;
        }

        $cluster = $component::getCluster();

        if (blank($cluster)) {
            return;
        }

        $this->clusteredComponents[$cluster][] = $component;
    }

    protected function queueLivewireComponentForRegistration(string $component): void
    {
        $componentName = app(ComponentRegistry::class)->getName($component);

        $this->livewireComponents[$componentName] = $component;
    }

    public function readOnlyRelationManagersOnResourceViewPagesByDefault(bool | Closure $condition = true): static
    {
        $this->hasReadOnlyRelationManagersOnResourceViewPagesByDefault = $condition;

        return $this;
    }

    public function hasReadOnlyRelationManagersOnResourceViewPagesByDefault(): bool
    {
        return (bool) $this->evaluate($this->hasReadOnlyRelationManagersOnResourceViewPagesByDefault);
    }

    /**
     * @return array<string | int, array<class-string> | class-string>
     */
    public function getClusteredComponents(?string $cluster = null): array
    {
        if (blank($cluster)) {
            return $this->clusteredComponents;
        }

        return $this->clusteredComponents[$cluster] ?? [];
    }

    public function hasCachedComponents(): bool
    {
        return $this->hasCachedComponents ??= ((! app()->runningInConsole()) && app(Filesystem::class)->exists($this->getComponentCachePath()));
    }

    public function cacheComponents(): void
    {
        $this->hasCachedComponents = false;

        $cachePath = $this->getComponentCachePath();

        $filesystem = app(Filesystem::class);

        $filesystem->ensureDirectoryExists((string) str($cachePath)->beforeLast(DIRECTORY_SEPARATOR));

        $filesystem->put(
            $cachePath,
            '<?php return ' . var_export([
                'livewireComponents' => $this->livewireComponents,
                'clusters' => $this->clusters,
                'clusteredComponents' => $this->clusteredComponents,
                'clusterDirectories' => $this->clusterDirectories,
                'clusterNamespaces' => $this->clusterNamespaces,
                'pages' => $this->pages,
                'pageDirectories' => $this->pageDirectories,
                'pageNamespaces' => $this->pageNamespaces,
                'resources' => $this->resources,
                'resourceDirectories' => $this->resourceDirectories,
                'resourceNamespaces' => $this->resourceNamespaces,
                'widgets' => $this->widgets,
                'widgetDirectories' => $this->widgetDirectories,
                'widgetNamespaces' => $this->widgetNamespaces,
            ], true) . ';',
        );

        $this->hasCachedComponents = true;
    }

    public function restoreCachedComponents(): void
    {
        if (! $this->hasCachedComponents()) {
            return;
        }

        $cache = require $this->getComponentCachePath();

        $this->livewireComponents = $cache['livewireComponents'] ?? [];
        $this->clusters = $cache['clusters'] ?? [];
        $this->clusteredComponents = $cache['clusteredComponents'] ?? [];
        $this->clusterDirectories = $cache['clusterDirectories'] ?? [];
        $this->clusterNamespaces = $cache['clusterNamespaces'] ?? [];
        $this->pages = $cache['pages'] ?? [];
        $this->pageDirectories = $cache['pageDirectories'] ?? [];
        $this->pageNamespaces = $cache['pageNamespaces'] ?? [];
        $this->resources = $cache['resources'] ?? [];
        $this->resourceDirectories = $cache['resourceDirectories'] ?? [];
        $this->resourceNamespaces = $cache['resourceNamespaces'] ?? [];
        $this->widgets = $cache['widgets'] ?? [];
        $this->widgetDirectories = $cache['widgetDirectories'] ?? [];
        $this->widgetNamespaces = $cache['widgetNamespaces'] ?? [];
    }

    public function clearCachedComponents(): void
    {
        app(Filesystem::class)->delete($this->getComponentCachePath());

        $this->hasCachedComponents = false;
    }

    public function getComponentCachePath(): string
    {
        return (config('filament.cache_path') ?? base_path('bootstrap/cache/filament')) . DIRECTORY_SEPARATOR . 'panels' . DIRECTORY_SEPARATOR . "{$this->getId()}.php";
    }

    public function resourceCreatePageRedirect(string | Closure | null $page): static
    {
        $this->resourceCreatePageRedirect = $page;

        return $this;
    }

    public function getResourceCreatePageRedirect(): ?string
    {
        return $this->evaluate($this->resourceCreatePageRedirect);
    }

    public function resourceEditPageRedirect(string | Closure | null $page): static
    {
        $this->resourceEditPageRedirect = $page;

        return $this;
    }

    public function getResourceEditPageRedirect(): ?string
    {
        return $this->evaluate($this->resourceEditPageRedirect);
    }
}
