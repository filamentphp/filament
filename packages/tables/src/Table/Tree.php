<?php

namespace Filament\Tables\Table;

use Closure;
use Filament\Support\Components\Component;
use Filament\Tables\Table\Concerns\BelongsToTable;

class Tree extends Component
{
    use BelongsToTable;

    protected bool | Closure $isEnabled = true;

    protected string | Closure | null $childrenRelationship = 'children';

    protected string | Closure | null $parentColumn = null;

    protected ?Closure $reorderUsing = null;

    protected bool | Closure $isCollapsible = true;

    protected bool | Closure $isCollapsedByDefault = false;

    protected string | Closure | null $titleColumn = null;

    protected string $evaluationIdentifier = 'tree';

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    public function enabled(bool | Closure $condition = true): static
    {
        $this->isEnabled = $condition;

        return $this;
    }

    public function collapsible(bool | Closure $condition = true): static
    {
        $this->isCollapsible = $condition;

        return $this;
    }

    public function collapsed(bool | Closure $condition = true): static
    {
        $this->isCollapsedByDefault = $condition;

        return $this;
    }

    public function childrenRelationship(string | Closure | null $relationship): static
    {
        $this->childrenRelationship = $relationship;

        return $this;
    }

    public function parentColumn(string | Closure | null $column): static
    {
        $this->parentColumn = $column;

        return $this;
    }

    public function titleColumn(string | Closure | null $column): static
    {
        $this->titleColumn = $column;

        return $this;
    }

    public function reorderUsing(?Closure $callback): static
    {
        $this->reorderUsing = $callback;

        return $this;
    }

    public function isEnabled(): bool
    {
        return (bool) $this->evaluate($this->isEnabled);
    }

    public function isCollapsible(): bool
    {
        return (bool) $this->evaluate($this->isCollapsible);
    }

    public function isCollapsedByDefault(): bool
    {
        return (bool) $this->evaluate($this->isCollapsedByDefault);
    }

    public function getChildrenRelationship(): string
    {
        return $this->evaluate($this->childrenRelationship) ?? 'children';
    }

    public function getParentColumn(): ?string
    {
        return $this->evaluate($this->parentColumn);
    }

    public function getTitleColumn(): ?string
    {
        return $this->evaluate($this->titleColumn);
    }

    public function getReorderUsing(): ?Closure
    {
        return $this->reorderUsing;
    }
}
