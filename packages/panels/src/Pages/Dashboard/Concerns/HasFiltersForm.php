<?php

namespace Filament\Pages\Dashboard\Concerns;

use Filament\Schema\Schema;

trait HasFiltersForm
{
    use HasFilters;

    protected function getHasFiltersForms(): array
    {
        return [
            'filtersForm' => $this->getFiltersForm(),
        ];
    }

    public function filtersForm(Schema $form): Schema
    {
        return $form;
    }

    public function getFiltersForm(): Schema
    {
        if ((! $this->isCachingSchemas) && $this->hasCachedSchema('filtersForm')) {
            return $this->getSchema('filtersForm');
        }

        return $this->filtersForm($this->makeSchema()
            ->extraAttributes(['wire:partial' => 'table-filters-form'])
            ->columns([
                'md' => 2,
                'xl' => 3,
                '2xl' => 4,
            ])
            ->statePath('filters')
            ->live());
    }
}
