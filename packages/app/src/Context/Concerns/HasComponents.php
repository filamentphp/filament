<?php

namespace Filament\Context\Concerns;

use Filament\Http\Livewire\GlobalSearch;
use Filament\Http\Livewire\Notifications;
use Filament\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Livewire\Component;
use Livewire\Livewire;
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

    protected ?string $pageDirectory = null;

    protected ?string $pageNamespace = null;

    /**
     * @var array<class-string>
     */
    protected array $resources = [];

    protected ?string $resourceDirectory = null;

    protected ?string $resourceNamespace = null;

    /**
     * @var array<class-string>
     */
    protected array $widgets = [];

    protected ?string $widgetDirectory = null;

    protected ?string $widgetNamespace = null;

    /**
     * @param  array<class-string>  $pages
     */
    public function pages(array $pages): static
    {
        $this->pages = array_merge($this->pages, $pages);

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
        $this->resources = array_merge($this->resources, $resources);

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
     * @param  array<class-string>  $widgets
     */
    public function widgets(array $widgets): static
    {
        $this->widgets = array_merge($this->widgets, $widgets);

        foreach ($widgets as $widget) {
            $this->queueLivewireComponentForRegistration($widget);
        }

        return $this;
    }

    public function discoverPages(string $in, string $for): static
    {
        $this->pageDirectory ??= $in;
        $this->pageNamespace ??= $for;

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
        $this->resourceDirectory ??= $in;
        $this->resourceNamespace ??= $for;

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
        $this->widgetDirectory ??= $in;
        $this->widgetNamespace ??= $for;

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
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return collect($this->widgets)
            ->unique()
            ->sortBy(fn (string $widget): int => $widget::getSort())
            ->all();
    }

    /**
     * @param  array<string, class-string>  $register
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
                ->replace('*', $variableNamespace)
                ->replace(['/', '.php'], ['\\', '']);

            if ((new ReflectionClass($class))->isAbstract()) {
                continue;
            }

            if (is_subclass_of($class, Component::class)) {
                $this->queueLivewireComponentForRegistration($class);
            }

            if (! is_subclass_of($class, $baseClass)) {
                continue;
            }

            if (! $class::isDiscovered()) {
                continue;
            }

            $register[] = $class;
        }
    }

    public function registerLivewireComponents(): void
    {
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
                        $this->queueLivewireComponentForRegistration($groupedRelation);
                    }

                    continue;
                }

                $this->queueLivewireComponentForRegistration($relation);
            }

            foreach ($resource::getWidgets() as $widget) {
                $this->queueLivewireComponentForRegistration($widget);
            }
        }

        foreach ($this->livewireComponents as $alias => $component) {
            Livewire::component($alias, $component);
        }

        $this->livewireComponents = [];
    }

    protected function queueLivewireComponentForRegistration(string $component): void
    {
        $this->livewireComponents[$component::getName()] = $component;
    }
}
