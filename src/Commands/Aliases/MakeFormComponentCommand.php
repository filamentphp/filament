<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeFormComponentCommand extends Commands\MakeFormComponentCommand
{
    protected $signature = 'filament:form-component {name} {--R|resource}';

    protected $hidden = true;
}
