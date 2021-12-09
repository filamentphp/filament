<?php

namespace Filament\Tables\Filters;

use Filament\Forms\Components\MultiSelect;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;

class MultiSelectFilter extends Filter
{
    protected ?string $column = null;

    protected bool $isStatic = false;

    protected array | Arrayable $options = [];

    public function apply(Builder $query, array $data = []): Builder
    {
        if ($this->isStatic) {
            return $query;
        }

        if ($this->hasQueryModificationCallback()) {
            return parent::apply($query, $data);
        }

        if (blank($data['value'])) {
            return $query;
        }

        $query->whereIn($this->getColumn(), $data['value']);

        return $query;
    }

    public function column(string $name): static
    {
        $this->column = $name;

        return $this;
    }

    public function options(array | Arrayable $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function static(bool $condition = true): static
    {
        $this->isStatic = $condition;

        return $this;
    }

    public function getColumn(): string
    {
        return $this->column ?? $this->getName();
    }

    public function getOptions(): array
    {
        $options = $this->options;

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        return $options;
    }

    public function getFormSchema(): array
    {
        return $this->formSchema ?? [
            MultiSelect::make('value')
                ->label($this->getLabel())
                ->options($this->getOptions()),
        ];
    }
}
