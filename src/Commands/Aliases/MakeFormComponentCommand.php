<?php

namespace Filament\Commands\Aliases;

use Filament\Commands;

class MakeFormComponentCommand extends Commands\MakeFormComponentCommand
{
    protected $hidden = true;
    protected $signature = 'filament:form-component {name} {--R|resource}';
}
