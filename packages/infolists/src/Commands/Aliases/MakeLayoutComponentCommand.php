<?php

namespace Filament\Infolists\Commands\Aliases;

use Filament\Infolists\Commands;

class MakeLayoutComponentCommand extends Commands\MakeLayoutComponentCommand
{
    protected $hidden = true;

    protected $signature = 'infolists:layout {name} {--F|force}';
}
