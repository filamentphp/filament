<?php

namespace Filament\Tables\Filters;

use Filament\Forms\Components\MultiSelect;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class MultiSelectFilter extends Filter
{
    protected ?string $column = null;

    protected bool $isStatic = false;

    protected array | Arrayable | null $options = null;

    public function apply(Builder $query, array $data = []): Builder
    {
        if ($this->isStatic) {
            return $query;
        }

        if ($this->hasQueryModificationCallback()) {
            return parent::apply($query, $data);
        }

        if (blank($data['values'] ?? null)) {
            return $query;
        }

        if ($this->queriesRelationships()) {
            /** @var BelongsTo $relationship */
            $relationship = $this->getRelationship();

            return $query->whereHas(
                $this->getRelationshipName(),
                fn (Builder $query) => $query->whereIn(
                    $relationship->getOwnerKeyName(),
                    $data['values'],
                ),
            );
        }

        /** @var Builder $query */
        $query = $query->whereIn($this->getColumn(), $data['values']);

        return $query;
    }

    public function column(string $name): static
    {
        $this->column = $name;

        return $this;
    }

    public function options(array | Arrayable | null $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function relationship(string $relationshipName, string $displayColumnName): static
    {
        $this->column("{$relationshipName}.{$displayColumnName}");

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

        if ($options === null) {
            $options = $this->queriesRelationships() ? $this->getRelationshipOptions() : [];
        }

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        return $options;
    }

    protected function getRelationshipOptions(): array
    {
        /** @var BelongsTo $relationship */
        $relationship = $this->getRelationship();

        $displayColumnName = $this->getRelationshipDisplayColumnName();

        $relationshipQuery = $relationship->getRelated()->query()->orderBy($displayColumnName);

        return $relationshipQuery
            ->pluck($displayColumnName, $relationship->getOwnerKeyName())
            ->toArray();
    }

    public function getFormSchema(): array
    {
        return $this->formSchema ?? [
            MultiSelect::make('values')
                ->label($this->getLabel())
                ->options($this->getOptions()),
        ];
    }

    public function queriesRelationships(): bool
    {
        return Str::of($this->getColumn())->contains('.');
    }

    protected function getRelationship(): Relation
    {
        $model = new ($this->getTable()->getModel())();

        return $model->{$this->getRelationshipName()}();
    }

    protected function getRelationshipName(): string
    {
        return (string) Str::of($this->getColumn())->beforeLast('.');
    }

    protected function getRelationshipDisplayColumnName(): string
    {
        return (string) Str::of($this->getColumn())->afterLast('.');
    }
}
