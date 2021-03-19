<?php

namespace Filament\Commands\Aliases;

class MakeFilterCommand extends \Filament\Commands\MakeFilterCommand
{
    protected $hidden = true;

    protected $signature = 'filament:filter {name} {--R|resource}';
}
