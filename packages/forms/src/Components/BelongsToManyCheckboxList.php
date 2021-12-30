<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class BelongsToManyCheckboxList extends CheckboxList
{
    protected string | Closure | null $displayColumnName = null;

    protected string | Closure | null $relationship = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (BelongsToManyCheckboxList $component, ?array $state): void {
            if (count($state ?? [])) {
                return;
            }

            $relationship = $component->getRelationship();
            $relatedModels = $relationship->getResults();

            if (! $relatedModels) {
                $component->state([]);

                return;
            }

            $component->state(
                $relatedModels->pluck(
                    $relationship->getRelatedKeyName(),
                )->toArray(),
            );
        });

        $this->saveRelationshipsUsing(function (BelongsToManyCheckboxList $component, array $state) {
            $component->getRelationship()->sync($state);
        });

        $this->dehydrated(false);
    }

    public function relationship(string | Closure $relationshipName, string | Closure $displayColumnName, ?Closure $callback = null): static
    {
        $this->displayColumnName = $displayColumnName;
        $this->relationship = $relationshipName;

        $this->options(function (BelongsToManyCheckboxList $component) use ($callback): array {
            $relationship = $component->getRelationship();

            $relationshipQuery = $relationship->getRelated()->query()->orderBy($component->getDisplayColumnName());

            if ($callback) {
                $relationshipQuery = $this->evaluate($callback, [
                    'query' => $relationshipQuery,
                ]);
            }

            return $relationshipQuery
                ->pluck($component->getDisplayColumnName(), $relationship->getRelatedKeyName())
                ->toArray();
        });

        return $this;
    }

    public function getDisplayColumnName(): string
    {
        return $this->evaluate($this->displayColumnName);
    }

    public function getLabel(): string
    {
        if ($this->label === null) {
            return (string) Str::of($this->getRelationshipName())
                ->before('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();
        }

        return parent::getLabel();
    }

    public function getRelationship(): BelongsToMany
    {
        $model = $this->getModel();

        if (is_string($model)) {
            $model = new $model();
        }

        return $model->{$this->getRelationshipName()}();
    }

    public function getRelationshipName(): string
    {
        return $this->evaluate($this->relationship);
    }
}
