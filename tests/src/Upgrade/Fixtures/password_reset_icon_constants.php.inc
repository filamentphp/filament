<?php

namespace Filament\Tests\Upgrade\Fixtures\PasswordResetIconConstants;

use Filament\View\PanelsIconAlias as Icons;

class CustomIcons extends Icons
{
    const PAGES_PASSWORD_RESET_REQUEST_PASSWORD_RESET_ACTIONS_LOGIN = 'custom::login';
}

return [
    Icons::PAGES_PASSWORD_RESET_REQUEST_PASSWORD_RESET_ACTIONS_LOGIN,
    Icons::PAGES_PASSWORD_RESET_REQUEST_PASSWORD_RESET_ACTIONS_LOGIN_RTL,
    Icons::AUTH_PASSWORD_RESET_REQUEST_ACTIONS_LOGIN,
    CustomIcons::PAGES_PASSWORD_RESET_REQUEST_PASSWORD_RESET_ACTIONS_LOGIN,
    'panels::pages.password-reset.request-password-reset.actions.login',
    'Filament\View\PanelsIconAlias::PAGES_PASSWORD_RESET_REQUEST_PASSWORD_RESET_ACTIONS_LOGIN',
];
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\PasswordResetIconConstants;

use Filament\View\PanelsIconAlias as Icons;

class CustomIcons extends Icons
{
    const PAGES_PASSWORD_RESET_REQUEST_PASSWORD_RESET_ACTIONS_LOGIN = 'custom::login';
}

return [
    Icons::AUTH_PASSWORD_RESET_REQUEST_ACTIONS_LOGIN,
    Icons::AUTH_PASSWORD_RESET_REQUEST_ACTIONS_LOGIN_RTL,
    Icons::AUTH_PASSWORD_RESET_REQUEST_ACTIONS_LOGIN,
    CustomIcons::PAGES_PASSWORD_RESET_REQUEST_PASSWORD_RESET_ACTIONS_LOGIN,
    'panels::pages.password-reset.request-password-reset.actions.login',
    'Filament\View\PanelsIconAlias::PAGES_PASSWORD_RESET_REQUEST_PASSWORD_RESET_ACTIONS_LOGIN',
];
