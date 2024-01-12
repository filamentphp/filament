<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeResourceCommand extends Commands\MakeResourceCommand
{
    protected $hidden = true;

    protected $signature = 'filament:resource {name?} {--model-namespace=} {--soft-deletes} {--view} {--G|generate} {--S|simple} {--panel=} {--F|force}';
}
