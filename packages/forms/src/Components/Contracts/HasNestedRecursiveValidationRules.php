<?php

namespace Filament\Forms\Components\Contracts;

interface HasNestedRecursiveValidationRules
{
    public function getNestedRecursiveValidationRules(): array;
}
