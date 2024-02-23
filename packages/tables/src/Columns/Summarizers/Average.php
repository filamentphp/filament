<?php

namespace Filament\Tables\Columns\Summarizers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;

class Average extends Summarizer
{
    protected ?string $selectAlias = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->numeric();
    }

    public function summarize(Builder $query, string $attribute): int | float | null
    {
        return $query->avg($attribute);
    }

    /**
     * @return array<string, string>
     */
    public function getSelectStatements(string $column): array
    {
        return [
            $this->getSelectAlias() => "avg({$column})",
        ];
    }

    public function getSelectedState(): int | float | null
    {
        if (! array_key_exists($this->selectAlias, $this->selectedState)) {
            return null;
        }

        return $this->selectedState[$this->getSelectAlias()];
    }

    public function selectAlias(?string $alias): static
    {
        $this->selectAlias = $alias;

        return $this;
    }

    public function getSelectAlias(): string
    {
        return $this->selectAlias ??= Str::random();
    }

    public function getDefaultLabel(): ?string
    {
        return __('filament-tables::table.summary.summarizers.average.label');
    }
}
