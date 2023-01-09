<?php

namespace Filament;

use Closure;
use Filament\Support\Facades\FilamentIcon;

class Context
{
    use Concerns\HasAuth;
    use Concerns\HasAvatars;
    use Concerns\HasBrandName;
    use Concerns\HasColors;
    use Concerns\HasComponents;
    use Concerns\HasDarkMode;
    use Concerns\HasFavicon;
    use Concerns\HasFont;
    use Concerns\HasGlobalSearch;
    use Concerns\HasIcons;
    use Concerns\HasId;
    use Concerns\HasMiddleware;
    use Concerns\HasNavigation;
    use Concerns\HasNotifications;
    use Concerns\HasPlugins;
    use Concerns\HasRenderHooks;
    use Concerns\HasRoutes;
    use Concerns\HasSidebar;
    use Concerns\HasTenancy;
    use Concerns\HasTheme;
    use Concerns\HasTopNavigation;
    use Concerns\HasUserMenu;

    protected bool $isDefault = false;

    protected ?Closure $bootUsing = null;

    public function default(bool $condition = true): static
    {
        $this->isDefault = $condition;

        return $this;
    }

    public function boot(): void
    {
        FilamentIcon::register($this->icons);
        $this->registerLivewireComponents();

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
