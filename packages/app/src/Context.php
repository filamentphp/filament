<?php

namespace Filament;

use Closure;
use Exception;
use Filament\AvatarProviders\UiAvatarsProvider;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Filament\GlobalSearch\DefaultGlobalSearchProvider;
use Filament\Http\Livewire\GlobalSearch;
use Filament\Http\Livewire\Notifications;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;
use Filament\Pages\Auth\Login;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Filament\Pages\Auth\PasswordReset\ResetPassword;
use Filament\Pages\Auth\Register;
use Filament\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Widgets\Widget;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Livewire\Livewire;
use ReflectionClass;

class Context
{
    protected string $id;

    protected bool $isDefault = false;

    protected string $authGuard = 'web';

    protected ?string $domain = null;

    protected string $globalSearchProvider = DefaultGlobalSearchProvider::class;

    protected string $homeUrl = '';

    protected bool $isNavigationMounted = false;

    protected array $livewireComponents = [];

    protected ?string $emailVerificationPromptPage = null;

    protected ?string $loginPage = null;

    protected ?string $registrationPage = null;

    protected ?string $requestPasswordResetPage = null;

    protected ?string $resetPasswordPage = null;

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

    protected bool $isEmailVerificationRequired = false;

    protected bool $isTenantSubscriptionRequired = false;

    protected ?Billing\Providers\Contracts\Provider $tenantBillingProvider = null;

    protected ?string $tenantModel = null;

    protected ?string $tenantRegistrationPage = null;

    protected ?string $tenantSlugField = null;

    protected string | Htmlable | null $theme = null;

    protected array $tenantMenuItems = [];

    protected array $userMenuItems = [];

    protected array $widgets = [];

    protected ?string $widgetDirectory = null;

    protected ?string $widgetNamespace = null;

    protected ?Closure $navigationBuilder = null;

    protected array $renderHooks = [];

    protected ?Closure $routes = null;

    protected ?Closure $authenticatedRoutes = null;

    protected ?Closure $authenticatedTenantRoutes = null;

    protected array $middleware = [];

    protected array $authMiddleware = [];

    protected bool $hasDarkMode = true;

    protected string $defaultAvatarProvider = UiAvatarsProvider::class;

    protected ?string $brandName = null;

    protected bool $hasDatabaseNotifications = false;

    protected ?string $databaseNotificationsPolling = '30s';

    protected ?string $googleFonts = 'https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&display=swap';

    protected array $plugins = [];

    public function default(bool $condition = true): static
    {
        $this->isDefault = $condition;

        return $this;
    }

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
        foreach ($this->plugins as $plugin) {
            $plugin->boot($this);
        }

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

    public function emailVerification(?string $promptPage = EmailVerificationPrompt::class, bool $isRequired = true): static
    {
        $this->emailVerificationPromptPage = $promptPage;
        $this->requiresEmailVerification($isRequired);

        return $this;
    }

    public function requiresEmailVerification(bool $condition = true): static
    {
        $this->isEmailVerificationRequired = $condition;

        return $this;
    }

    public function login(?string $page = Login::class): static
    {
        $this->loginPage = $page;

        return $this;
    }

    public function passwordReset(?string $requestPage = RequestPasswordReset::class, ?string $resetPage = ResetPassword::class): static
    {
        $this->requestPasswordResetPage = $requestPage;
        $this->resetPasswordPage = $resetPage;

        return $this;
    }

    public function registration(?string $page = Register::class): static
    {
        $this->registrationPage = $page;

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

        foreach ($pages as $page) {
            $this->queueLivewireComponentForRegistration($page);
        }

        return $this;
    }

    public function path(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function requiresTenantSubscription(bool $condition = true): static
    {
        $this->isTenantSubscriptionRequired = $condition;

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

    public function viteTheme(string | array $theme, ?string $buildDirectory = null): static
    {
        $this->theme(app(Vite::class)($theme, $buildDirectory));

        return $this;
    }

    public function theme(string | Htmlable | null $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function tenantMenuItems(array $items): static
    {
        $this->tenantMenuItems = array_merge($this->tenantMenuItems, $items);

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

        foreach ($widgets as $widget) {
            $this->queueLivewireComponentForRegistration($widget);
        }

        return $this;
    }

    public function meta(array $meta): static
    {
        $this->meta = array_merge($this->meta, $meta);

        return $this;
    }

    public function routes(?Closure $routes): static
    {
        $this->routes = $routes;

        return $this;
    }

    public function authenticatedRoutes(?Closure $routes): static
    {
        $this->authenticatedRoutes = $routes;

        return $this;
    }

    public function authenticatedTenantRoutes(?Closure $routes): static
    {
        $this->authenticatedTenantRoutes = $routes;

        return $this;
    }

    public function middleware(array $middleware): static
    {
        $this->middleware = array_merge(
            $this->middleware,
            $middleware,
        );

        return $this;
    }

    public function authMiddleware(array $middleware): static
    {
        $this->authMiddleware = array_merge(
            $this->authMiddleware,
            $middleware,
        );

        return $this;
    }

    public function tenant(?string $model, ?string $slugField = null): static
    {
        $this->tenantModel = $model;
        $this->tenantSlugField = $slugField;

        return $this;
    }

    public function tenantBillingProvider(?Billing\Providers\Contracts\Provider $provider): static
    {
        $this->tenantBillingProvider = $provider;

        return $this;
    }

    public function tenantRegistration(?string $page): static
    {
        $this->tenantRegistrationPage = $page;

        return $this;
    }

    public function darkMode(bool $condition = true): static
    {
        $this->hasDarkMode = $condition;

        return $this;
    }

    public function avatarProvider(string $provider): static
    {
        $this->defaultAvatarProvider = $provider;

        return $this;
    }

    public function brandName(?string $name): static
    {
        $this->brandName = $name;

        return $this;
    }

    public function databaseNotifications(bool $condition = true): static
    {
        $this->hasDatabaseNotifications = $condition;

        return $this;
    }

    public function databaseNotificationsPolling(?string $interval): static
    {
        $this->databaseNotificationsPolling = $interval;

        return $this;
    }

    public function googleFonts(?string $url): static
    {
        $this->googleFonts = $url;

        return $this;
    }

    public function plugin(Plugin $plugin): static
    {
        $this->plugins[$plugin->getId()] = $plugin;

        return $this;
    }

    public function plugins(array $plugins): static
    {
        foreach ($plugins as $plugin) {
            $this->plugin($plugin);
        }

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

    public function isEmailVerificationRequired(): bool
    {
        return $this->isEmailVerificationRequired;
    }

    public function isTenantSubscriptionRequired(): bool
    {
        return $this->isTenantSubscriptionRequired;
    }

    public function hasTenantBilling(): bool
    {
        return filled($this->getTenantBillingProvider());
    }

    public function hasTenantRegistration(): bool
    {
        return filled($this->getTenantRegistrationPage());
    }

    public function getTenantBillingProvider(): ?Billing\Providers\Contracts\Provider
    {
        return $this->tenantBillingProvider;
    }

    public function getTenantRegistrationPage(): ?string
    {
        return $this->tenantRegistrationPage;
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

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function getAuthGuard(): ?string
    {
        return $this->authGuard;
    }

    public function getRenderHook(string $name): Htmlable
    {
        $hooks = array_map(
            fn (callable $hook): string => (string) app()->call($hook),
            $this->renderHooks[$name] ?? [],
        );

        return new HtmlString(implode('', $hooks));
    }

    public function getTenantModel(): ?string
    {
        return $this->tenantModel;
    }

    public function getTenantSlugField(): ?string
    {
        return $this->tenantSlugField;
    }

    public function getEmailVerificationPromptUrl(): ?string
    {
        if (! $this->hasEmailVerification()) {
            return null;
        }

        return route($this->getEmailVerificationPromptRouteName());
    }

    public function getEmailVerificationPromptRouteName(): string
    {
        return "filament.{$this->getId()}.auth.email-verification.prompt";
    }

    public function getEmailVerifiedMiddleware(): string
    {
        return "verified:{$this->getEmailVerificationPromptRouteName()}";
    }

    public function getLoginUrl(): ?string
    {
        if (! $this->hasLogin()) {
            return null;
        }

        return route("filament.{$this->getId()}.auth.login");
    }

    public function getRegistrationUrl(): ?string
    {
        if (! $this->hasRegistration()) {
            return null;
        }

        return route("filament.{$this->getId()}.auth.register");
    }

    public function getRequestPasswordResetUrl(): ?string
    {
        if (! $this->hasPasswordReset()) {
            return null;
        }

        return route("filament.{$this->getId()}.auth.password-reset.request");
    }

    public function getVerifyEmailUrl(MustVerifyEmail | Model | Authenticatable $user): string
    {
        return URL::temporarySignedRoute(
            "filament.{$this->getId()}.auth.email-verification.verify",
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ],
        );
    }

    public function getResetPasswordUrl(string $token, CanResetPassword | Model | Authenticatable $user): string
    {
        return URL::signedRoute("filament.{$this->getId()}.auth.password-reset.reset", [
            'email' => $user->getEmailForPasswordReset(),
            'token' => $token,
        ]);
    }

    public function getTenantBillingUrl(Model $tenant): ?string
    {
        if (! $this->hasTenantBilling()) {
            return null;
        }

        return route("filament.{$this->getId()}.tenant.billing", [
            'tenant' => $tenant,
        ]);
    }

    public function getTenantRegistrationUrl(): ?string
    {
        if (! $this->hasTenantRegistration()) {
            return null;
        }

        return route("filament.{$this->getId()}.tenant.registration");
    }

    public function getLogoutUrl(): string
    {
        return route("filament.{$this->getId()}.auth.logout");
    }

    public function getMiddleware(): array
    {
        return array_merge(
            ["context:{$this->getId()}"],
            $this->middleware,
        );
    }

    public function getAuthMiddleware(): array
    {
        return $this->authMiddleware;
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

    public function getRoutes(): ?Closure
    {
        return $this->routes;
    }

    public function getAuthenticatedRoutes(): ?Closure
    {
        return $this->authenticatedRoutes;
    }

    public function getAuthenticatedTenantRoutes(): ?Closure
    {
        return $this->authenticatedTenantRoutes;
    }

    public function getTenantMenuItems(): array
    {
        return collect($this->tenantMenuItems)
            ->sort(fn (MenuItem $item): int => $item->getSort())
            ->all();
    }

    public function getUserMenuItems(): array
    {
        return collect($this->userMenuItems)
            ->sort(fn (MenuItem $item): int => $item->getSort())
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

        if ((! $tenant) && $this->hasRoutableTenancy()) {
            $tenant = Filament::getUserDefaultTenant($this->auth()->user());
        }

        if ($tenant && $this->hasRoutableTenancy()) {
            $originalTenant = Filament::getTenant();
            Filament::setTenant($tenant);

            $isNavigationMountedOriginally = $this->isNavigationMounted;
            $originalNavigationItems = $this->navigationItems;
            $originalNavigationGroups = $this->navigationGroups;

            $this->isNavigationMounted = false;
            $this->navigationItems = [];
            $this->navigationGroups = [];

            $navigation = $this->getNavigation();

            Filament::setTenant($originalTenant);

            $this->isNavigationMounted = $isNavigationMountedOriginally;
            $this->navigationItems = $originalNavigationItems;
            $this->navigationGroups = $originalNavigationGroups;
        } else {
            $navigation = $this->getNavigation();
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

    public function getEmailVerificationPromptPage(): ?string
    {
        return $this->emailVerificationPromptPage;
    }

    public function getLoginPage(): ?string
    {
        return $this->loginPage;
    }

    public function getRegistrationPage(): ?string
    {
        return $this->registrationPage;
    }

    public function getRequestPasswordResetPage(): ?string
    {
        return $this->requestPasswordResetPage;
    }

    public function getResetPasswordPage(): ?string
    {
        return $this->resetPasswordPage;
    }

    public function hasEmailVerification(): bool
    {
        return filled($this->getEmailVerificationPromptPage());
    }

    public function hasLogin(): bool
    {
        return filled($this->getLoginPage());
    }

    public function hasPasswordReset(): bool
    {
        return filled($this->getRequestPasswordResetPage());
    }

    public function hasRegistration(): bool
    {
        return filled($this->getRegistrationPage());
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

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function hasDarkMode(): bool
    {
        return $this->hasDarkMode;
    }

    public function getDefaultAvatarProvider(): string
    {
        return $this->defaultAvatarProvider;
    }

    public function getBrandName(): string
    {
        return $this->brandName ?? config('app.name');
    }

    public function hasDatabaseNotifications(): bool
    {
        return $this->hasDatabaseNotifications;
    }

    public function getDatabaseNotificationsPollingInterval(): ?string
    {
        return $this->databaseNotificationsPolling;
    }

    public function getGoogleFonts(): ?string
    {
        return $this->googleFonts;
    }

    public function getPlugins(): array
    {
        return $this->plugins;
    }

    public function getPlugin(string $id): Plugin
    {
        return $this->getPlugins()[$id] ?? throw new Exception("Plugin [{$id}] is not registered for context [{$this->getId()}].");
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

        if ($this->hasEmailVerification()) {
            $this->queueLivewireComponentForRegistration($this->getEmailVerificationPromptPage());
        }

        if ($this->hasLogin()) {
            $this->queueLivewireComponentForRegistration($this->getLoginPage());
        }

        if ($this->hasPasswordReset()) {
            $this->queueLivewireComponentForRegistration($this->getRequestPasswordResetPage());
            $this->queueLivewireComponentForRegistration($this->getResetPasswordPage());
        }

        if ($this->hasRegistration()) {
            $this->queueLivewireComponentForRegistration($this->getRegistrationPage());
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
