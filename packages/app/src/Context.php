<?php

namespace Filament;

use Closure;
use Exception;
use Filament\AvatarProviders\UiAvatarsProvider;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\FontProviders\BunnyFontProvider;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Filament\GlobalSearch\DefaultGlobalSearchProvider;
use Filament\Http\Livewire\GlobalSearch;
use Filament\Http\Livewire\Notifications;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;
use Filament\Pages\Auth\Login;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Filament\Pages\Auth\PasswordReset\ResetPassword;
use Filament\Pages\Auth\Register;
use Filament\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Support\Assets\Theme;
use Filament\Support\Color;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Icon;
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
    use Concerns\HasAuth;
    use Concerns\HasAvatars;
    use Concerns\HasBrandName;
    use Concerns\HasColors;
    use Concerns\HasComponents;
    use Concerns\HasDarkMode;
    use Concerns\HasFavicon;
    use Concerns\HasFont;
    use Concerns\HasGlobalSearch;
    use Concerns\HasHeadTags;
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
