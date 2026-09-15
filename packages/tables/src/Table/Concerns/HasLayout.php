<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tables\Components\TableContent;
use Filament\Tables\Components\TableFilters;
use Filament\Tables\Components\TablePart;
use Filament\Tables\Enums\FiltersLayout;
use Livewire\Component as LivewireComponent;
use LogicException;

trait HasLayout
{
    protected Schema | Closure | null $layout = null;

    protected bool | Closure $isContained = true;

    protected ?Schema $cachedLayout = null;

    public function layout(Schema | Closure | null $layout): static
    {
        $this->layout = $layout;
        $this->cachedLayout = null;

        return $this;
    }

    public function contained(bool | Closure $condition = true): static
    {
        $this->isContained = $condition;

        return $this;
    }

    public function isContained(): bool
    {
        return (bool) $this->evaluate($this->isContained);
    }

    public function hasCustomLayout(): bool
    {
        return $this->layout !== null;
    }

    public function getLayout(): Schema
    {
        if (! $this->hasCustomLayout()) {
            return $this->getDefaultLayout();
        }

        return $this->cachedLayout ??= $this->makeCustomLayout();
    }

    protected function makeCustomLayout(): Schema
    {
        $schema = Schema::make($this->getLivewireWithSchemas());

        return $this->evaluate($this->layout, ['schema' => $schema], [Schema::class => $schema]) ?? $schema;
    }

    public function hasFooter(): bool
    {
        return $this->hasPagination()
            || $this->hasEmptyState()
            || ($this->isFilterable() && ($this->getFiltersLayout() === FiltersLayout::BelowContent));
    }

    public function getDefaultLayout(): Schema
    {
        throw new LogicException('The default table layout is not available as a schema yet.');
    }

    /**
     * Renders the layout's components one after another, without the `<div class="fi-sc">`
     * wrapper that `Schema::toHtml()` would add, so the table markup stays unchanged.
     */
    public function renderLayout(Schema $layout): string
    {
        return Component::withVisibilityCache(function () use ($layout): string {
            $html = '';

            foreach ($layout->getComponents(withHidden: true) as $component) {
                if (! $component->isVisible()) {
                    continue;
                }

                $html .= $component->toHtml();
            }

            return $html;
        });
    }

    public function assertLayoutIsComplete(Schema $layout): void
    {
        $parts = $this->getLayoutParts($layout);

        if (! array_filter($parts, fn (TablePart $part): bool => $part instanceof TableContent)) {
            throw new LogicException('The table layout does not contain a [' . TableContent::class . '] part, so no records would be rendered. Add `TableContent::make()` to the schema passed to `$table->layout()`.');
        }

        // The default layout places `TableFilters` in every position and lets `FiltersLayout` pick one.
        $duplicatePartClasses = array_keys(array_filter(
            array_count_values(array_map(
                fn (TablePart $part): string => $part::class,
                array_filter($parts, fn (TablePart $part): bool => ! ($part instanceof TableFilters)),
            )),
            fn (int $count): bool => $count > 1,
        ));

        if ($duplicatePartClasses !== []) {
            throw new LogicException('The table layout contains the [' . $duplicatePartClasses[0] . '] part more than once. Each part may appear only once in the schema passed to `$table->layout()`.');
        }
    }

    /**
     * @return array<TablePart>
     */
    protected function getLayoutParts(Schema $schema): array
    {
        $parts = [];

        foreach ($schema->getComponents(withHidden: true) as $component) {
            if (! ($component instanceof Component)) {
                continue;
            }

            if ($component instanceof TablePart) {
                $parts[] = $component;
            }

            foreach ($component->getChildSchemas(withHidden: true) as $childSchema) {
                $parts = [...$parts, ...$this->getLayoutParts($childSchema)];
            }
        }

        return $parts;
    }

    protected function getLivewireWithSchemas(): LivewireComponent & HasSchemas
    {
        $livewire = $this->getLivewire();

        if (! (($livewire instanceof LivewireComponent) && ($livewire instanceof HasSchemas))) {
            throw new LogicException('The table layout requires the Livewire component [' . $livewire::class . '] to implement [' . HasSchemas::class . '] and use the `InteractsWithSchemas` trait.');
        }

        return $livewire;
    }
}
