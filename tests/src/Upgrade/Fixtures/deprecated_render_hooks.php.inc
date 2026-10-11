<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedRenderHooks;

use Filament\Tables\View\TablesRenderHook as Hooks;

class CustomRenderHook
{
    const TOOLBAR_TOGGLE_COLUMN_TRIGGER_AFTER = 'custom::after';
}

return [
    Hooks::TOOLBAR_TOGGLE_COLUMN_TRIGGER_AFTER => 'after',
    Hooks::TOOLBAR_TOGGLE_COLUMN_TRIGGER_BEFORE => 'before',
    Hooks::TOOLBAR_COLUMN_MANAGER_TRIGGER_AFTER => 'canonical',
    'tables::toolbar.toggle-column-trigger.before' => 'string',
    CustomRenderHook::TOOLBAR_TOGGLE_COLUMN_TRIGGER_AFTER => 'custom',
];
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedRenderHooks;

use Filament\Tables\View\TablesRenderHook as Hooks;

class CustomRenderHook
{
    const TOOLBAR_TOGGLE_COLUMN_TRIGGER_AFTER = 'custom::after';
}

return [
    Hooks::TOOLBAR_COLUMN_MANAGER_TRIGGER_AFTER => 'after',
    Hooks::TOOLBAR_COLUMN_MANAGER_TRIGGER_BEFORE => 'before',
    Hooks::TOOLBAR_COLUMN_MANAGER_TRIGGER_AFTER => 'canonical',
    'tables::toolbar.toggle-column-trigger.before' => 'string',
    CustomRenderHook::TOOLBAR_TOGGLE_COLUMN_TRIGGER_AFTER => 'custom',
];
