<?php

namespace Filament\Pages\Dashboard\Concerns;

use Filament\Schema\ComponentContainer;

trait HasFiltersForm
{
    use HasFilters;

    protected function getHasFiltersForms(): array
    {
        return [
            'filtersForm' => $this->getFiltersForm(),
        ];
    }

    public function filtersForm(ComponentContainer $form): ComponentContainer
    {
        return $form;
    }

    public function getFiltersForm(): ComponentContainer
    {
        if ((! $this->isCachingSchemas) && $this->hasCachedSchema('filtersForm')) {
            return $this->getSchema('filtersForm');
        }

        return $this->filtersForm($this->makeForm()
            ->columns([
                'md' => 2,
                'xl' => 3,
                '2xl' => 4,
            ])
            ->statePath('filters')
            ->live());
    }
}
