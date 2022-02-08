<?php

namespace Filament\Forms\Commands\Aliases;

use Filament\Forms\Commands;

class MakeFieldCommand extends Commands\MakeFieldCommand
{
    protected $hidden = true;

    protected $signature = 'forms:field {name} {--F|force}';
}
