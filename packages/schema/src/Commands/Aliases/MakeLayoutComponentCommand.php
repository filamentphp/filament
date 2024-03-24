<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeLayoutComponentCommand extends Commands\MakeLayoutComponentCommand
{
    protected $hidden = true;

    protected $signature = 'forms:layout {name} {--F|force}';
}
