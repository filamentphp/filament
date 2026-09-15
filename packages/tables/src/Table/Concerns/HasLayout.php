<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tables\Components\TableContent;
use Filament\Tables\Components\TablePart;
use Livewire\Component as LivewireComponent;
use LogicException;

trait HasLayout
{
    protected Schema | Closure | null $layout = null;

    protected bool | Closure $isContained = true;

    public function layout(Schema | Closure | null $layout): static
    {
        $this->layout = $layout;

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

        $schema = Schema::make($this->getLivewireWithSchemas());

        return $this->evaluate($this->layout, ['schema' => $schema], [Schema::class => $schema]) ?? $schema;
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
        $partClasses = $this->getLayoutPartClasses($layout);

        if (! in_array(TableContent::class, $partClasses, strict: true)) {
            throw new LogicException('The table layout does not contain a [' . TableContent::class . '] part, so no records would be rendered. Add `TableContent::make()` to the schema passed to `$table->layout()`.');
        }

        $duplicatePartClasses = array_keys(array_filter(
            array_count_values($partClasses),
            fn (int $count): bool => $count > 1,
        ));

        if ($duplicatePartClasses !== []) {
            throw new LogicException('The table layout contains the [' . $duplicatePartClasses[0] . '] part more than once. Each part may appear only once in the schema passed to `$table->layout()`.');
        }
    }

    /**
     * @return array<class-string<TablePart>>
     */
    protected function getLayoutPartClasses(Schema $schema): array
    {
        $partClasses = [];

        foreach ($schema->getComponents(withHidden: true) as $component) {
            if (! ($component instanceof Component)) {
                continue;
            }

            if ($component instanceof TablePart) {
                $partClasses[] = $component::class;
            }

            foreach ($component->getChildSchemas(withHidden: true) as $childSchema) {
                $partClasses = [...$partClasses, ...$this->getLayoutPartClasses($childSchema)];
            }
        }

        return $partClasses;
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
