<?php

namespace Filament\Forms\Commands\Aliases;

use Filament\Forms\Commands;

class MakeLayoutComponentCommand extends Commands\MakeLayoutComponentCommand
{
    protected $hidden = true;

    protected $signature = 'forms:layout {name} {--F|force}';
}
