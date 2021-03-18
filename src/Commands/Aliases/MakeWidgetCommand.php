<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeWidgetCommand extends Commands\MakeWidgetCommand
{
    protected $hidden = true;

    protected $signature = 'filament:widget {name}';
}
