<?php

namespace Filament\Widgets\ChartWidget\Concerns;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\View\WidgetsIconAlias;

trait HasFiltersSchema /** @phpstan-ignore trait.unused */
{
    public ?array $filters = [];

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema;
    }

    public function getFiltersTriggerAction(): Action
    {
        return Action::make('filter')
            ->label(__('filament-widgets::chart.actions.filter.label'))
            ->iconButton()
            ->icon(FilamentIcon::resolve(WidgetsIconAlias::CHART_WIDGET_FILTER) ?? Heroicon::Funnel)
            ->color('gray')
            ->livewireClickHandlerEnabled(false);
    }

    public function getFiltersSchema(): Schema
    {
        if ((! $this->isCachingSchemas) && $this->hasCachedSchema('filtersSchema')) {
            return $this->getSchema('filtersSchema');
        }

        return $this->filtersSchema($this->makeSchema()
            ->statePath('filters')
            ->live());
    }
}
