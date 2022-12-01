<?php

namespace Filament\Forms\Components\Contracts;

interface HasValidationRules
{
    /**
     * @param  array<string, string>  $attributes
     */
    public function dehydrateValidationAttributes(array &$attributes): void;

    /**
     * @param  array<string, array<mixed>>  $rules
     */
    public function dehydrateValidationRules(array &$rules): void;
}
