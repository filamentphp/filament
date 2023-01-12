<?php

namespace Filament;

use Closure;
use Filament\Support\Facades\FilamentIcon;

class Context
{
    use Context\Concerns\HasAuth;
    use Context\Concerns\HasAvatars;
    use Context\Concerns\HasBrandName;
    use Context\Concerns\HasColors;
    use Context\Concerns\HasComponents;
    use Context\Concerns\HasDarkMode;
    use Context\Concerns\HasFavicon;
    use Context\Concerns\HasFont;
    use Context\Concerns\HasGlobalSearch;
    use Context\Concerns\HasIcons;
    use Context\Concerns\HasId;
    use Context\Concerns\HasMiddleware;
    use Context\Concerns\HasNavigation;
    use Context\Concerns\HasNotifications;
    use Context\Concerns\HasPlugins;
    use Context\Concerns\HasRenderHooks;
    use Context\Concerns\HasRoutes;
    use Context\Concerns\HasSidebar;
    use Context\Concerns\HasTenancy;
    use Context\Concerns\HasTheme;
    use Context\Concerns\HasTopNavigation;
    use Context\Concerns\HasUserMenu;

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
