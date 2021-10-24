<?php

namespace Filament\Tables\Filters;

use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

class SelectFilter extends Filter
{
    protected ?string $column = null;

    protected array $options = [];

    public function apply(Builder $query, array $data = []): Builder
    {
        if ($this->hasQueryModificationCallback()) {
            return parent::apply($query, $data);
        }

        if (! $data['value']) {
            return $query;
        }

        $query->where($this->getColumn(), $data['value']);

        return $query;
    }

    public function column(string $name): static
    {
        $this->column = $name;

        return $this;
    }

    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getColumn(): string
    {
        return $this->column ?? $this->getName();
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getFormSchema(): array
    {
        return $this->formSchema ?? [
            Select::make('value')
                ->label($this->getLabel())
                ->options($this->getOptions()),
        ];
    }
}
