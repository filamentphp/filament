<?php

namespace Filament\Widgets\ChartWidget\Concerns;

use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\View\WidgetsIconAlias;

trait HasFiltersSchema /** @phpstan-ignore trait.unused */
{
    /**
     * @var array<string, mixed> | null
     */
    public ?array $filters = [];

    /**
     * @var array<string, mixed> | null
     */
    public ?array $deferredFilters = null;

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema;
    }

    public function hasDeferredFilters(): bool
    {
        return property_exists($this, 'hasDeferredFilters') && $this->hasDeferredFilters;
    }

    public function mountHasFiltersSchema(): void
    {
        $this->getFiltersSchema()->fill();

        if ($this->hasDeferredFilters()) {
            $this->filters = $this->deferredFilters;
        }
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

        return $this->filtersSchema($this->makeSchema())
            ->when(
                $this->hasDeferredFilters(),
                fn (Schema $schema) => $schema
                    ->statePath('deferredFilters')
                    ->partiallyRender(),
                fn (Schema $schema) => $schema
                    ->statePath('filters')
                    ->live(),
            );
    }

    public function updatedFilters(): void
    {
        $this->cachedData = null;
    }

    public function applyFilters(): void
    {
        $this->filters = $this->deferredFilters;
        $this->cachedData = null;
    }

    public function resetFiltersForm(): void
    {
        $this->getFiltersSchema()->fill();

        if ($this->hasDeferredFilters()) {
            $this->applyFilters();

            return;
        }

        $this->cachedData = null;
    }

    public function getFiltersApplyAction(): Action
    {
        $action = Action::make('applyFilters')
            ->label(__('filament-widgets::chart.filters.actions.apply.label'))
            ->action('applyFilters')
            ->button();

        if (method_exists($this, 'filtersApplyAction')) {
            $action = $this->filtersApplyAction($action);
        }

        return $action;
    }

    public function getFiltersResetAction(): Action
    {
        $action = Action::make('resetFilters')
            ->label(__('filament-widgets::chart.filters.actions.reset.label'))
            ->action('resetFiltersForm')
            ->color('danger')
            ->button();

        if (method_exists($this, 'filtersResetAction')) {
            $action = $this->filtersResetAction($action);
        }

        return $action;
    }
}
