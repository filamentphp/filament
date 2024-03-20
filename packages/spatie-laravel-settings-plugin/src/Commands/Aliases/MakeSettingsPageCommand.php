<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeSettingsPageCommand extends Commands\MakeSettingsPageCommand
{
    protected $hidden = true;

    protected $signature = 'filament:settings-page {name?} {settingsClass?} {--panel=} {--F|force}';
}
