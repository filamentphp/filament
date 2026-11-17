<?php

namespace Filament\Widgets\ChartWidget\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\View\WidgetsIconAlias;

trait HasFiltersForm
{
    /**
     * @var array<string, mixed> | null
     */
    public ?array $filters = [];

    /**
     * @var array<string, mixed> | null
     */
    public ?array $deferredFilters = null;

    #[Unexposed]
    public function filtersForm(Schema $schema): Schema
    {
        // Preserve existing `filtersSchema()` overrides, including `parent::` calls.
        return $this->filtersSchema($schema);
    }

    /**
     * @deprecated Use `filtersForm()` instead.
     */
    #[Unexposed]
    public function filtersSchema(Schema $schema): Schema
    {
        return $schema;
    }

    #[Unexposed]
    public function hasDeferredFilters(): bool
    {
        return property_exists($this, 'hasDeferredFilters') && $this->hasDeferredFilters;
    }

    #[Unexposed]
    public function mountHasFiltersForm(): void
    {
        $this->getFiltersForm()->fill();

        if ($this->hasDeferredFilters()) {
            $this->filters = $this->deferredFilters;
        }
    }

    #[Unexposed]
    public function getFiltersTriggerAction(): Action
    {
        return Action::make('filter')
            ->label(__('filament-widgets::chart.actions.filter.label'))
            ->iconButton()
            ->icon(FilamentIcon::resolve(WidgetsIconAlias::CHART_WIDGET_FILTER) ?? Heroicon::Funnel)
            ->color('gray')
            ->livewireClickHandlerEnabled(false);
    }

    #[Unexposed]
    public function getFiltersForm(): Schema
    {
        return $this->getFiltersSchema();
    }

    /**
     * @deprecated Use `getFiltersForm()` instead.
     */
    #[Unexposed]
    public function getFiltersSchema(): Schema
    {
        if ((! $this->isCachingSchemas) && $this->hasCachedSchema('filtersSchema')) {
            return $this->getSchema('filtersSchema');
        }

        $schema = $this->makeSchema();

        if ($this->isCachingSchemas && method_exists($this, 'defaultFiltersSchema')) {
            $schema = $this->defaultFiltersSchema($schema);
        }

        return $this->filtersForm($schema)
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

    protected function cacheSchema(string $name, Schema | Closure | null $schema = null): ?Schema
    {
        if (($name === 'filtersSchema') && (func_num_args() === 1)) {
            // Preserve the schema key while resolving canonical configuration on every request.
            if (! in_array($name, $this->discoveredSchemaNames)) {
                $this->discoveredSchemaNames[] = $name;
            }

            return parent::cacheSchema($name, fn (): Schema => $this->getFiltersForm());
        }

        return parent::cacheSchema(...func_get_args());
    }

    #[Unexposed]
    public function updatedFilters(): void
    {
        $this->cachedData = null;
    }

    #[Exposed]
    public function applyFilters(): void
    {
        $this->filters = $this->deferredFilters;
        $this->cachedData = null;
    }

    /**
     * @deprecated Use `resetFilters()` instead.
     */
    #[Exposed]
    public function resetFiltersForm(): void
    {
        $this->resetFilters();
    }

    #[Exposed]
    public function resetFilters(): void
    {
        $this->getFiltersForm()->fill();

        if ($this->hasDeferredFilters()) {
            $this->applyFilters();

            return;
        }

        $this->cachedData = null;
    }

    #[Unexposed]
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

    #[Unexposed]
    public function getFiltersResetAction(): Action
    {
        $action = Action::make('resetFilters')
            ->label(__('filament-widgets::chart.filters.actions.reset.label'))
            ->action('resetFilters')
            ->color('danger')
            ->button();

        if (method_exists($this, 'filtersResetAction')) {
            $action = $this->filtersResetAction($action);
        }

        return $action;
    }
}
