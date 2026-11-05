<?php

namespace NamingConsistencyUpgrade;

use Closure;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Panel;
use Filament\Support\Concerns\CanBeLazy;

trait CustomLoading
{
    use CanBeLazy;
}

class CustomPanel extends Panel {}

class CustomEmailAuthentication extends EmailAuthentication {}

class OverriddenEmailAuthentication extends EmailAuthentication
{
    public function generateCodesUsing(?Closure $callback): static
    {
        return $this;
    }
}

class Unrelated
{
    public function getResetPasswordRoutePrefix(): string
    {
        return 'unchanged';
    }

    public function generateCodesUsing(?Closure $callback): static
    {
        return $this;
    }
}

function configure(Panel $panel, ?EmailAuthentication $emailAuthentication, CustomPanel $customPanel, CustomEmailAuthentication $customEmailAuthentication, Unrelated $unrelated): void
{
    $panel->getResetPasswordRoutePrefix();
    Panel::make()->passwordResetRoutePrefix('recovery')->getResetPasswordRoutePrefix();
    $emailAuthentication?->generateCodesUsing(callback: static fn (): string => '012345');
    EmailAuthentication::make()->generateCodesUsing(null)->generateCode();
    $configuredProvider = EmailAuthentication::make()->generateCodesUsing(null);
    $configuredProvider->generateCodesUsing(callback: null)->generateCodesUsing(null);
    $codeCallback = $configuredProvider->generateCodesUsing(...);
    $customPanel->getResetPasswordRoutePrefix();
    $customEmailAuthentication->generateCodesUsing(null);
    $getPrefix = $panel->getResetPasswordRoutePrefix(...);
    $configureCode = $emailAuthentication?->generateCodesUsing(null);
    (static fn (): Panel => $panel)()->getResetPasswordRoutePrefix();
    array_map(static fn (EmailAuthentication $provider) => $provider->generateCodesUsing(null), [$emailAuthentication]);
    Panel::configureUsing(static fn (Panel $configuredPanel) => $configuredPanel->getResetPasswordRoutePrefix());
    $unrelated->getResetPasswordRoutePrefix();
    $unrelated->generateCodesUsing(null);
    (new OverriddenEmailAuthentication)->generateCodesUsing(null);
}
-----
<?php

namespace NamingConsistencyUpgrade;

use Filament\Support\Concerns\CanBeLazyLoaded;
use Closure;
use Filament\Auth\MultiFactor\Email\EmailAuthentication;
use Filament\Panel;

trait CustomLoading
{
    use CanBeLazyLoaded;
}

class CustomPanel extends Panel {}

class CustomEmailAuthentication extends EmailAuthentication {}

class OverriddenEmailAuthentication extends EmailAuthentication
{
    public function generateCodesUsing(?Closure $callback): static
    {
        return $this;
    }
}

class Unrelated
{
    public function getResetPasswordRoutePrefix(): string
    {
        return 'unchanged';
    }

    public function generateCodesUsing(?Closure $callback): static
    {
        return $this;
    }
}

function configure(Panel $panel, ?EmailAuthentication $emailAuthentication, CustomPanel $customPanel, CustomEmailAuthentication $customEmailAuthentication, Unrelated $unrelated): void
{
    $panel->getPasswordResetRoutePrefix();
    Panel::make()->passwordResetRoutePrefix('recovery')->getPasswordResetRoutePrefix();
    $emailAuthentication?->generateCodeUsing(callback: static fn (): string => '012345');
    EmailAuthentication::make()->generateCodeUsing(null)->generateCode();
    $configuredProvider = EmailAuthentication::make()->generateCodeUsing(null);
    $configuredProvider->generateCodeUsing(callback: null)->generateCodeUsing(null);
    $codeCallback = $configuredProvider->generateCodeUsing(...);
    $customPanel->getPasswordResetRoutePrefix();
    $customEmailAuthentication->generateCodeUsing(null);
    $getPrefix = $panel->getPasswordResetRoutePrefix(...);
    $configureCode = $emailAuthentication?->generateCodeUsing(null);
    (static fn (): Panel => $panel)()->getPasswordResetRoutePrefix();
    array_map(static fn (EmailAuthentication $provider) => $provider->generateCodeUsing(null), [$emailAuthentication]);
    Panel::configureUsing(static fn (Panel $configuredPanel) => $configuredPanel->getPasswordResetRoutePrefix());
    $unrelated->getResetPasswordRoutePrefix();
    $unrelated->generateCodesUsing(null);
    (new OverriddenEmailAuthentication)->generateCodesUsing(null);
}
