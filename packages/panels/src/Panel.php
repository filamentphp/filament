<?php

namespace Filament;

use Closure;
use Filament\Actions\MountableAction;
use Filament\Support\Components\Component;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Route;

class Panel extends Component
{
    use Panel\Concerns\CanGenerateResourceUrls;
    use Panel\Concerns\HasAssets;
    use Panel\Concerns\HasAuth;
    use Panel\Concerns\HasAvatars;
    use Panel\Concerns\HasBrandLogo;
    use Panel\Concerns\HasBrandName;
    use Panel\Concerns\HasBreadcrumbs;
    use Panel\Concerns\HasBroadcasting;
    use Panel\Concerns\HasColors;
    use Panel\Concerns\HasComponents;
    use Panel\Concerns\HasDarkMode;
    use Panel\Concerns\HasDatabaseTransactions;
    use Panel\Concerns\HasFavicon;
    use Panel\Concerns\HasFont;
    use Panel\Concerns\HasGlobalSearch;
    use Panel\Concerns\HasIcons;
    use Panel\Concerns\HasId;
    use Panel\Concerns\HasMaxContentWidth;
    use Panel\Concerns\HasMiddleware;
    use Panel\Concerns\HasNavigation;
    use Panel\Concerns\HasNotifications;
    use Panel\Concerns\HasPlugins;
    use Panel\Concerns\HasRenderHooks;
    use Panel\Concerns\HasRoutes;
    use Panel\Concerns\HasSidebar;
    use Panel\Concerns\HasSpaMode;
    use Panel\Concerns\HasTenancy;
    use Panel\Concerns\HasTheme;
    use Panel\Concerns\HasTopbar;
    use Panel\Concerns\HasTopNavigation;
    use Panel\Concerns\HasUnsavedChangesAlerts;
    use Panel\Concerns\HasUserMenu;

    protected bool $isDefault = false;

    /**
     * @var array<array-key, Closure>
     */
    protected array $bootCallbacks = [];

    public static function make(): static
    {
        return app(static::class);
    }

    public function default(bool $condition = true): static
    {
        $this->isDefault = $condition;

        return $this;
    }

    public function register(): void
    {
        $this->registerLivewireComponents();
        $this->registerLivewirePersistentMiddleware();

        if (str($this->getTenantDomain())->is(['{tenant}', '{tenant:*}'])) {
            // Laravel does not match periods in route parameters by default.
            Route::pattern('tenant', '[a-z0-9.\-]+');
        }

        if (app()->runningInConsole()) {
            $this->registerAssets();
        }
    }

    public function boot(): void
    {
        $this->registerAssets();

        FilamentColor::register($this->getColors());

        FilamentIcon::register($this->getIcons());

        FilamentView::spa($this->hasSpaMode());
        FilamentView::spaUrlExceptions($this->getSpaUrlExceptions());

        $this->registerRenderHooks();

        if ($this->hasDatabaseTransactions()) {
            MountableAction::configureUsing(
                fn (MountableAction $action) => $action->databaseTransaction(),
            );
        }

        foreach ($this->plugins as $plugin) {
            $plugin->boot($this);
        }

        foreach ($this->bootCallbacks as $callback) {
            $callback($this);
        }
    }

    public function bootUsing(?Closure $callback): static
    {
        $this->bootCallbacks[] = $callback;

        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }
}
