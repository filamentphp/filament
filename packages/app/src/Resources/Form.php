<?php

namespace Filament\Resources;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Wizard;

class Form
{
    protected array | int | null $columns = null;

    protected bool $isDisabled = false;

    protected bool $isWizard = false;

    protected ?Closure $modifyBaseComponentUsing = null;

    protected array | Component | Closure $schema = [];

    final public function __construct()
    {
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function columns(array | int | null $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function disabled(bool $condition = true): static
    {
        $this->isDisabled = $condition;

        return $this;
    }

    public function modifyBaseComponentUsing(?Closure $callback): static
    {
        $this->modifyBaseComponentUsing = $callback;

        return $this;
    }

    public function schema(array | Component | Closure $schema): static
    {
        $this->schema = $schema;

        return $this;
    }

    public function wizard(bool $condition = true): static
    {
        $this->isWizard = $condition;

        return $this;
    }

    public function getColumns(): array | int | null
    {
        return $this->columns;
    }

    public function isDisabled(): bool
    {
        return $this->isDisabled;
    }

    public function isWizard(): bool
    {
        return $this->isWizard;
    }

    public function modifyBaseComponent(Component $component): void
    {
        if (! $this->modifyBaseComponentUsing) {
            return;
        }

        ($this->modifyBaseComponentUsing)($component);
    }

    public function getSchema(): array
    {
        if ($this->schema instanceof Component) {
            return [$this->schema];
        }

        $baseComponent = $this->isWizard() ? Wizard::make() : Grid::make();

        $baseComponent
            ->schema($this->schema)
            ->columns($this->getColumns())
            ->disabled($this->isDisabled());

        $this->modifyBaseComponent($baseComponent);

        return [$baseComponent];
    }
}
