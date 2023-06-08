<?php

namespace Filament\Widgets\Commands\Aliases;

use Filament\Widgets\Commands;

class MakeWidgetCommand extends Commands\MakeWidgetCommand
{
    protected $hidden = true;

    protected $signature = 'filament:widget {name?} {--R|resource=} {--C|chart} {--T|table} {--S|stats-overview} {--panel=} {--F|force}';
}
