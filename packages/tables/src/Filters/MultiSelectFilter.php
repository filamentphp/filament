<?php

namespace Filament\Tables\Filters;

use Closure;
use Filament\Forms\Components\MultiSelect;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class MultiSelectFilter extends Filter
{
    use Concerns\HasOptions;
    use Concerns\HasPlaceholder;

    protected string | Closure | null $column = null;

    protected bool | Closure $isStatic = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->placeholder(__('tables::table.filters.multi_select.placeholder'));
    }

    public function apply(Builder $query, array $data = []): Builder
    {
        if ($this->evaluate($this->isStatic)) {
            return $query;
        }

        if ($this->hasQueryModificationCallback()) {
            return parent::apply($query, $data);
        }

        if (blank($data['values'] ?? null)) {
            return $query;
        }

        if ($this->queriesRelationships()) {
            $relationship = $this->getRelationship();

            if ($relationship instanceof MorphToMany) {
                /** @var MorphToMany $relationship */
                $column = $relationship->getParentKeyName();
            } else {
                /** @var BelongsTo $relationship */
                $column = $relationship->getOwnerKeyName();
            }

            return $query->whereHas(
                $this->getRelationshipName(),
                fn (Builder $query) => $query->whereIn(
                    $column,
                    $data['values'],
                ),
            );
        }

        /** @var Builder $query */
        $query = $query->whereIn($this->getColumn(), $data['values']);

        return $query;
    }

    public function column(string | Closure | null $name): static
    {
        $this->column = $name;

        return $this;
    }

    public function relationship(string $relationshipName, string $displayColumnName = null): static
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

    protected function getRelationshipOptions(): array
    {
        $relationship = $this->getRelationship();

        if ($relationship instanceof MorphToMany) {
            /** @var MorphToMany $relationship */
            $keyColumn = $relationship->getParentKeyName();
        } else {
            /** @var BelongsTo $relationship */
            $keyColumn = $relationship->getOwnerKeyName();
        }

        $displayColumnName = $this->getRelationshipDisplayColumnName();

        $relationshipQuery = $relationship->getRelated()->query()->orderBy($displayColumnName);

        return $relationshipQuery
            ->pluck($displayColumnName, $keyColumn)
            ->toArray();
    }

    public function getFormSchema(): array
    {
        return $this->formSchema ?? [
            MultiSelect::make('values')
                ->label($this->getLabel())
                ->options($this->getOptions())
                ->placeholder($this->getPlaceholder())
                ->default($this->getDefaultState()),
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
