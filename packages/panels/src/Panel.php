<?php

namespace Filament;

use Closure;
use Filament\Support\Components\Component;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;

class Panel extends Component
{
    use Panel\Concerns\HasAuth;
    use Panel\Concerns\HasAvatars;
    use Panel\Concerns\HasBrandLogo;
    use Panel\Concerns\HasBrandName;
    use Panel\Concerns\HasBreadcrumbs;
    use Panel\Concerns\HasColors;
    use Panel\Concerns\HasComponents;
    use Panel\Concerns\HasDarkMode;
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
    use Panel\Concerns\HasTopNavigation;
    use Panel\Concerns\HasUserMenu;

    protected bool $isDefault = false;

    protected ?Closure $bootUsing = null;

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
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
    }

    public function boot(): void
    {
        FilamentColor::register($this->colors);

        FilamentIcon::register($this->icons);

        FilamentView::spa($this->hasSpaMode());

        $this->registerRenderHooks();

        foreach ($this->plugins as $plugin) {
            $plugin->boot($this);
        }

        if ($callback = $this->bootUsing) {
            $callback($this);
        }
    }

    public function bootUsing(?Closure $callback): static
    {
        $this->bootUsing = $callback;

        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }
}
