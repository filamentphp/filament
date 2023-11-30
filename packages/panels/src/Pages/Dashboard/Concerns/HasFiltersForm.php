<?php

namespace Filament\Pages\Dashboard\Concerns;

use Filament\Forms\Form;

trait HasFiltersForm
{
    use HasFilters;

    protected function getHasFiltersForms(): array
    {
        return [
            'filtersForm' => $this->getFiltersForm(),
        ];
    }

    public function filtersForm(Form $form): Form
    {
        return $form;
    }

    public function getFiltersForm(): Form
    {
        if ((! $this->isCachingForms) && $this->hasCachedForm('filtersForm')) {
            return $this->getForm('filtersForm');
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
