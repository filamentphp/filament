<?php

namespace Filament;

use Closure;
use Exception;
use Filament\Events\ServingFilament;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Filament\GlobalSearch\DefaultGlobalSearchProvider;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Navigation\UserMenuItem;
use Filament\Notifications\Facades\Notification as NotificationFacade;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class FilamentManager
{
    protected string $globalSearchProvider = DefaultGlobalSearchProvider::class;

    protected bool $isNavigationMounted = false;

    protected array $navigationGroups = [];

    protected array $navigationItems = [];

    protected array $pages = [];

    protected array $resources = [];

    protected array $beforeCoreScripts = [];

    protected array $scripts = [];

    protected array $scriptData = [];

    protected array $styles = [];

    protected array $meta = [];

    protected string | Htmlable | null $theme = null;

    protected array $userMenuItems = [];

    protected array $widgets = [];

    protected ?Closure $navigationBuilder = null;

    protected array $renderHooks = [];

    public function auth(): Guard
    {
        return auth()->guard(config('filament.auth.guard'));
    }

    public function navigation(Closure $builder): void
    {
        $this->navigationBuilder = $builder;
    }

    public function buildNavigation(): array
    {
        /** @var \Filament\Navigation\NavigationBuilder $builder */
        $builder = app()->call($this->navigationBuilder);

        return collect([
            null => [
                'items' => $builder->getItems(),
                'collapsible' => false,
            ],
        ])
            ->merge($builder->getGroups())
            ->toArray();
    }

    public function globalSearchProvider(string $provider): void
    {
        if (! in_array(GlobalSearchProvider::class, class_implements($provider))) {
            throw new Exception("Global search provider {$provider} does not implement the " . GlobalSearchProvider::class . ' interface.');
        }

        $this->globalSearchProvider = $provider;
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

    public function registerNavigationGroups(array $groups): void
    {
        $this->navigationGroups = array_merge($this->navigationGroups, $groups);
    }

    public function registerNavigationItems(array $items): void
    {
        $this->navigationItems = array_merge($this->navigationItems, $items);
    }

    public function registerPages(array $pages): void
    {
        $this->pages = array_merge($this->pages, $pages);
    }

    public function registerRenderHook(string $name, Closure $callback): void
    {
        $this->renderHooks[$name][] = $callback;
    }

    public function registerResources(array $resources): void
    {
        $this->resources = array_merge($this->resources, $resources);
    }

    public function registerScripts(array $scripts, bool $shouldBeLoadedBeforeCoreScripts = false): void
    {
        if ($shouldBeLoadedBeforeCoreScripts) {
            $this->beforeCoreScripts = array_merge($this->beforeCoreScripts, $scripts);
        } else {
            $this->scripts = array_merge($this->scripts, $scripts);
        }
    }

    public function registerScriptData(array $data): void
    {
        $this->scriptData = array_merge($this->scriptData, $data);
    }

    public function registerStyles(array $styles): void
    {
        $this->styles = array_merge($this->styles, $styles);
    }

    public function registerTheme(string | Htmlable | null $theme): void
    {
        $this->theme = $theme;
    }

    public function registerUserMenuItems(array $items): void
    {
        $this->userMenuItems = array_merge($this->userMenuItems, $items);
    }

    public function registerWidgets(array $widgets): void
    {
        $this->widgets = array_merge($this->widgets, $widgets);
    }

    public function pushMeta(array $meta): void
    {
        $this->meta = array_merge($this->meta, $meta);
    }

    public function serving(Closure $callback): void
    {
        Event::listen(ServingFilament::class, $callback);
    }

    /**
     * @deprecated Use \Filament\Notifications\Facades\Notification::notify() instead.
     */
    public function notify(Notification | string $status, ?string $message = null, bool $isAfterRedirect = false): void
    {
        if (! $status instanceof Notification) {
            $status = Notification::make()
                ->title($message)
                ->status($status);
        }

        NotificationFacade::send($status);
    }

    public function getGlobalSearchProvider(): GlobalSearchProvider
    {
        return app($this->globalSearchProvider);
    }

    public function renderHook(string $name): Htmlable
    {
        $hooks = array_map(
            fn (callable $hook): string => (string) app()->call($hook),
            $this->renderHooks[$name] ?? [],
        );

        return new HtmlString(implode('', $hooks));
    }

    public function getNavigation(): array
    {
        if ($this->navigationBuilder !== null) {
            return $this->buildNavigation();
        }

        if (! $this->isNavigationMounted) {
            $this->mountNavigation();
        }

        $groupedItems = collect($this->navigationItems)
            ->sortBy(fn (Navigation\NavigationItem $item): int => $item->getSort())
            ->groupBy(fn (Navigation\NavigationItem $item): ?string => $item->getGroup());

        $sortedGroups = $groupedItems
            ->keys()
            ->sortBy(function (?string $group): int {
                if (! $group) {
                    return -1;
                }

                $sort = array_search($group, $this->navigationGroups);

                if ($sort === false) {
                    return count($this->navigationGroups);
                }

                return $sort;
            });

        return $sortedGroups
            ->mapWithKeys(function (?string $group) use ($groupedItems): array {
                return [
                    $group => [
                        'items' => $groupedItems->get($group),
                        'collapsible' => config('filament.layout.sidebar.groups.are_collapsible'),
                    ],
                ];
            })
            ->toArray();
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
            ->toArray();
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

    public function getScripts(): array
    {
        return $this->scripts;
    }

    public function getBeforeCoreScripts(): array
    {
        return $this->beforeCoreScripts;
    }

    public function getScriptData(): array
    {
        return $this->scriptData;
    }

    public function getStyles(): array
    {
        return $this->styles;
    }

    public function getThemeLink(): Htmlable
    {
        if (Str::of($this->theme)->contains('<link')) {
            return $this->theme instanceof Htmlable ? $this->theme : new HtmlString($this->theme);
        }

        $url = $this->theme ?? route('filament.asset', [
            'id' => get_asset_id('app.css'),
            'file' => 'app.css',
        ]);

        return new HtmlString("<link rel=\"stylesheet\" href=\"{$url}\" />");
    }

    public function getUrl(): ?string
    {
        $flatNavigation = Arr::flatten($this->getNavigation());

        $firstItem = $flatNavigation[0] ?? null;

        if (! $firstItem) {
            return null;
        }

        return $firstItem->getUrl();
    }

    public function getUserAvatarUrl(Model $user): string
    {
        $avatar = null;

        if ($user instanceof HasAvatar) {
            $avatar = $user->getFilamentAvatarUrl();
        }

        if ($avatar) {
            return $avatar;
        }

        $provider = config('filament.default_avatar_provider');

        return app($provider)->get($user);
    }

    public function getUserName(Model $user): string
    {
        if ($user instanceof HasName) {
            return $user->getFilamentName();
        }

        return $user->getAttributeValue('name');
    }

    public function getWidgets(): array
    {
        return collect($this->widgets)
            ->unique()
            ->sortBy(fn (string $widget): int => $widget::getSort())
            ->toArray();
    }

    public function getMeta(): array
    {
        return array_unique($this->meta);
    }
}
