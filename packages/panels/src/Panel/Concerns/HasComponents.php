<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Livewire\DatabaseNotifications;
use Filament\Livewire\GlobalSearch;
use Filament\Livewire\Notifications;
use Filament\Pages\Auth\EditProfile;
use Filament\Pages\Page;
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

    /**
     * @param  array<class-string>  $pages
     */
    public function pages(array $pages): static
    {
        $this->pages = [
            ...$this->pages,
            ...$pages,
        ];

        foreach ($pages as $page) {
            $this->queueLivewireComponentForRegistration($page);
        }

        return $this;
    }

    /**
     * @param  array<class-string>  $resources
     */
    public function resources(array $resources): static
    {
        $this->resources = [
            ...$this->resources,
            ...$resources,
        ];

        return $this;
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

    /**
     * @param  array<class-string<Widget> | WidgetConfiguration>  $widgets
     */
    public function widgets(array $widgets): static
    {
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

    public function discoverResources(string $in, string $for): static
    {
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
        $this->widgetDirectories[] = $in;
        $this->widgetNamespaces[] = $for;

        $this->discoverComponents(
            Widget::class,
            $this->widgets,
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
        $component = [];

        $this->discoverComponents(
            Component::class,
            $component,
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

        foreach ($filesystem->allFiles($directory) as $file) {
            $variableNamespace = $namespace->contains('*') ? str_ireplace(
                ['\\' . $namespace->before('*'), $namespace->after('*')],
                ['', ''],
                str($file->getPath())
                    ->after(base_path())
                    ->replace(['/'], ['\\']),
            ) : null;

            if (is_string($variableNamespace)) {
                $variableNamespace = (string) str($variableNamespace)->before('\\');
            }

            $class = (string) $namespace
                ->append('\\', $file->getRelativePathname())
                ->replace('*', $variableNamespace ?? '')
                ->replace(['/', '.php'], ['\\', '']);

            if (! class_exists($class)) {
                continue;
            }

            if ((new ReflectionClass($class))->isAbstract()) {
                continue;
            }

            if (is_subclass_of($class, Component::class)) {
                $this->queueLivewireComponentForRegistration($class);
            }

            if (! is_subclass_of($class, $baseClass)) {
                continue;
            }

            if (
                method_exists($class, 'isDiscovered') &&
                (! $class::isDiscovered())
            ) {
                continue;
            }

            $register[] = $class;
        }
    }

    /**
     * @param  array<string, class-string<Component>>  $components
     */
    public function livewireComponents(array $components): static
    {
        foreach ($components as $component) {
            $this->queueLivewireComponentForRegistration($component);
        }

        return $this;
    }

    protected function registerLivewireComponents(): void
    {
        $this->queueLivewireComponentForRegistration(DatabaseNotifications::class);
        $this->queueLivewireComponentForRegistration(EditProfile::class);
        $this->queueLivewireComponentForRegistration(GlobalSearch::class);
        $this->queueLivewireComponentForRegistration(Notifications::class);

        if ($this->hasEmailVerification() && is_subclass_of($emailVerificationRouteAction = $this->getEmailVerificationPromptRouteAction(), Component::class)) {
            $this->queueLivewireComponentForRegistration($emailVerificationRouteAction);
        }

        if ($this->hasLogin() && is_subclass_of($loginRouteAction = $this->getLoginRouteAction(), Component::class)) {
            $this->queueLivewireComponentForRegistration($loginRouteAction);
        }

        if ($this->hasPasswordReset()) {
            if (is_subclass_of($requestPasswordResetRouteAction = $this->getRequestPasswordResetRouteAction(), Component::class)) {
                $this->queueLivewireComponentForRegistration($requestPasswordResetRouteAction);
            }

            if (is_subclass_of($resetPasswordRouteAction = $this->getResetPasswordRouteAction(), Component::class)) {
                $this->queueLivewireComponentForRegistration($resetPasswordRouteAction);
            }
        }

        if ($this->hasRegistration() && is_subclass_of($registrationRouteAction = $this->getRegistrationRouteAction(), Component::class)) {
            $this->queueLivewireComponentForRegistration($registrationRouteAction);
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

        foreach ($this->livewireComponents as $componentName => $componentClass) {
            Livewire::component($componentName, $componentClass);
        }

        $this->livewireComponents = [];
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
}
