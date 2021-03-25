<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\Components\Select;

trait CanHandleSelectFields
{
    public function getSelectFieldOptionSearchResults($fieldName, $search = '')
    {
        $field = collect($this->getForm()->getFlatSchema())
            ->first(fn ($field) => $field instanceof Select && $field->getName() === $fieldName);

        if (! $field) {
            return [];
        }

        return $field->getOptionSearchResults($search);
    }
}
