<?php

namespace Filament\Tables\Commands\Aliases;

use Filament\Tables\Commands;

class MakeColumnCommand extends Commands\MakeColumnCommand
{
    protected $hidden = true;

    protected $signature = 'tables:column {name} {--F|force}';
}
