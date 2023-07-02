<?php

namespace Filament\Tables\Columns\Summarizers;

use Exception;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;

class Count extends Summarizer
{
    protected bool $hasIcons = false;

    protected ?string $selectAlias = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->numeric();
    }

    /**
     * @return int | float | array<string, array<string, int>> | null
     */
    public function summarize(Builder $query, string $attribute): int | float | array | null
    {
        if (! $this->hasIcons) {
            return $query->count();
        }

        $column = $this->getColumn();

        if (! ($column instanceof IconColumn)) {
            throw new Exception("The [{$column->getName()}] column must be an IconColumn to show an icon count summary.");
        }

        $state = [];

        foreach ($query->clone()->pluck($attribute) as $value) {
            $column->record($this->getQuery()->getModel()->setAttribute($attribute, $value));
            $columnState = $column->getState();
            $color = json_encode($column->getColor($columnState));
            $icon = $column->getIcon($columnState);

            $state[$color] ??= [];
            $state[$color][$icon] ??= 0;

            $state[$color][$icon] += $query->clone()->where($attribute, $value)->count();
        }

        return $state;
    }

    /**
     * @return array<string, string>
     */
    public function getSelectStatements(string $column): array
    {
        if ($this->hasIcons) {
            return [];
        }

        return [
            $this->getSelectAlias() => "count({$column})",
        ];
    }

    public function getSelectedState(): int | float | null
    {
        if (! array_key_exists($this->selectAlias, $this->selectedState)) {
            return null;
        }

        return $this->selectedState[$this->getSelectAlias()];
    }

    public function getSelectAlias(): string
    {
        return $this->selectAlias ??= Str::random();
    }

    public function icons(bool $condition = true): static
    {
        $this->hasIcons = $condition;

        $this->view('filament-tables::columns.summaries.icon-count');

        return $this;
    }

    public function getDefaultLabel(): ?string
    {
        return $this->hasIcons ? null : __('filament-tables::table.summary.summarizers.count.label');
    }

    public function hasIcons(): bool
    {
        return $this->hasIcons;
    }
}
