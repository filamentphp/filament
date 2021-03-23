<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeUpgradeCommand extends Commands\MakeUpgradeCommand
{
    protected $hidden = true;

    protected $signature = 'filament:upgrade';
}
