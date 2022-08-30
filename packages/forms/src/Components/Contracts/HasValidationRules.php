<?php

namespace Filament\Forms\Components\Contracts;

interface HasValidationRules
{
    public function dehydrateValidationAttributes(array &$attributes): void;

    public function dehydrateValidationRules(array &$rules): void;
}
