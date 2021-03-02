<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeWidgetCommand extends Commands\MakeWidgetCommand
{
    protected $signature = 'filament:widget {name}';

    protected $hidden = true;
}
