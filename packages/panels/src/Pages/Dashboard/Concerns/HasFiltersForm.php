<?php

namespace Filament\Pages\Dashboard\Concerns;

use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\Unexposed;

trait HasFiltersForm /** @phpstan-ignore trait.unused */
{
    use HasFilters;

    #[Unexposed]
    public function bootedHasFiltersForm(): void
    {
        $this->cacheSchema('filtersForm', $this->getFiltersForm());
    }

    #[Unexposed]
    public function filtersForm(Schema $schema): Schema
    {
        return $schema;
    }

    #[Unexposed]
    public function getFiltersForm(): Schema
    {
        if ((! $this->isCachingSchemas) && $this->hasCachedSchema('filtersForm')) {
            return $this->getSchema('filtersForm');
        }

        $schema = $this->makeSchema()
            ->columns([
                'md' => 2,
                'xl' => 3,
                '2xl' => 4,
            ])
            ->extraAttributes(['wire:partial' => 'table-filters-form'])
            ->live()
            ->statePath('filters');

        return $this->filtersForm($schema);
    }
}
