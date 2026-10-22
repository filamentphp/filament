<?php

namespace Filament\Pages\Dashboard\Concerns;

use Filament\Schemas\Schema;
use Filament\Support\Attributes\ServerOnly;

trait HasFiltersForm /** @phpstan-ignore trait.unused */
{
    use HasFilters;

    #[ServerOnly]
    public function bootedHasFiltersForm(): void
    {
        $this->cacheSchema('filtersForm', $this->getFiltersForm());
    }

    #[ServerOnly]
    public function filtersForm(Schema $schema): Schema
    {
        return $schema;
    }

    #[ServerOnly]
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
