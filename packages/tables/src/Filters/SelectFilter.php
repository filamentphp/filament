<?php

namespace Filament\Tables\Filters;

use Closure;
use Filament\Forms\Components\Select;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class SelectFilter extends Filter
{
    use Concerns\HasPlaceholder;

    protected string | Closure | null $column = null;

    protected bool | Closure $isStatic = false;

    protected array | Arrayable | Closure | null $options = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->placeholder(__('tables::table.filters.select.placeholder'));
    }

    public function apply(Builder $query, array $data = []): Builder
    {
        if ($this->evaluate($this->isStatic)) {
            return $query;
        }

        if ($this->hasQueryModificationCallback()) {
            return parent::apply($query, $data);
        }

        if (blank($data['value'] ?? null)) {
            return $query;
        }

        if ($this->queriesRelationships()) {
            /** @var BelongsTo $relationship */
            $relationship = $this->getRelationship();

            return $query->whereRelation(
                $this->getRelationshipName(),
                $relationship->getOwnerKeyName(),
                $data['value'],
            );
        }

        return $query->where($this->getColumn(), $data['value']);
    }

    public function column(string | Closure | null $name): static
    {
        $this->column = $name;

        return $this;
    }

    public function options(array | Arrayable | Closure | null $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function relationship(string $relationshipName, string $displayColumnName): static
    {
        $this->column("{$relationshipName}.{$displayColumnName}");

        return $this;
    }

    public function static(bool | Closure $condition = true): static
    {
        $this->isStatic = $condition;

        return $this;
    }

    public function getColumn(): string
    {
        return $this->evaluate($this->column) ?? $this->getName();
    }

    public function getOptions(): array
    {
        $options = $this->evaluate($this->options);

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
            Select::make('value')
                ->label($this->getLabel())
                ->options($this->getOptions())
                ->placeholder($this->getPlaceholder()),
        ];
    }

    public function queriesRelationships(): bool
    {
        return Str::of($this->getColumn())->contains('.');
    }

    protected function getRelationship(): Relation
    {
        $model = app($this->getTable()->getModel());

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
