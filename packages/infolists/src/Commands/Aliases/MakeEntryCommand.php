<?php

namespace Filament\Infolists\Commands\Aliases;

use Filament\Infolists\Commands;

class MakeEntryCommand extends Commands\MakeEntryCommand
{
    protected $hidden = true;

    protected $signature = 'infolists:entry {name} {--F|force}';
}
