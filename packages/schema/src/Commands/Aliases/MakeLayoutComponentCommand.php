<?php

namespace Filament\Schema\Commands\Aliases;

use Filament\Schema\Commands;

class MakeLayoutComponentCommand extends Commands\MakeLayoutComponentCommand
{
    protected $hidden = true;

    protected $signature = 'forms:layout {name} {--F|force}';
}
