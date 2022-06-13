<?php

namespace Filament\Forms\Components;

use Closure;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BelongsToSelect extends Select
{
    protected string | Closure | null $titleColumnName = null;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    protected bool | Closure $isPreloaded = false;

    protected string | Closure | null $relationship = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadStateFromRelationshipsUsing(static function (BelongsToSelect $component, $state): void {
            if (filled($state)) {
                return;
            }

            $relationship = $component->getRelationship();
            $relatedModel = $relationship->getResults();

            if (! $relatedModel) {
                return;
            }

            $component->state(
                $relatedModel->getAttribute(
                    $relationship->getOwnerKeyName(),
                ),
            );
        });

        $this->getOptionLabelUsing(static function (BelongsToSelect $component, $value) {
            $relationship = $component->getRelationship();

            $record = $relationship->getRelated()->query()->where($relationship->getOwnerKeyName(), $value)->first();

            if (! $record) {
                return null;
            }

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $component->getOptionLabelFromRecord($record);
            }

            return $record->getAttributeValue($component->getTitleColumnName());
        });

        $this->exists(
            static fn (BelongsToSelect $component): string => $component->getRelationship()->getModel()::class,
            static fn (BelongsToSelect $component): string => $component->getRelationship()->getOwnerKeyName(),
        );

        $this->saveRelationshipsUsing(static function (BelongsToSelect $component, Model $record, $state) {
            $component->getRelationship()->associate($state);
            $record->save();
        });

        $this->createOptionUsing(static function (BelongsToSelect $component, array $data) {
            return $component->getRelationship()->create($data)->getKey();
        });
    }

    public function preload(bool | Closure $condition = true): static
    {
        $this->isPreloaded = $condition;

        return $this;
    }

    public function getSearchColumns(): array
    {
        return $this->searchColumns ?? [$this->getTitleColumnName()];
    }

    public function relationship(string | Closure $relationshipName, string | Closure $titleColumnName, ?Closure $callback = null): static
    {
        $this->titleColumnName = $titleColumnName;
        $this->relationship = $relationshipName;

        $this->getSearchResultsUsing(static function (BelongsToSelect $component, ?string $search) use ($callback): array {
            $relationship = $component->getRelationship();

            $relationshipQuery = $relationship->getRelated()->query()->orderBy($component->getTitleColumnName());

            if ($callback) {
                $relationshipQuery = $component->evaluate($callback, [
                    'query' => $relationshipQuery,
                ]);
            }

            $search = strtolower($search);

            $relationshipQuery = $component->applySearchConstraint($relationshipQuery, $search)->limit(50);

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $relationshipQuery
                    ->get()
                    ->mapWithKeys(static fn (Model $record) => [
                        $record->{$relationship->getOwnerKeyName()} => $component->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            return $relationshipQuery
                ->pluck($component->getTitleColumnName(), $relationship->getOwnerKeyName())
                ->toArray();
        });

        $this->options(static function (BelongsToSelect $component) use ($callback): array {
            if ($component->isSearchable() && ! $component->isPreloaded()) {
                return [];
            }

            $relationship = $component->getRelationship();

            $relationshipQuery = $relationship->getRelated()->query()->orderBy($component->getTitleColumnName());

            if ($callback) {
                $relationshipQuery = $component->evaluate($callback, [
                    'query' => $relationshipQuery,
                ]);
            }

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $relationshipQuery
                    ->get()
                    ->mapWithKeys(static fn (Model $record) => [
                        $record->{$relationship->getOwnerKeyName()} => $component->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            return $relationshipQuery
                ->pluck($component->getTitleColumnName(), $relationship->getOwnerKeyName())
                ->toArray();
        });

        return $this;
    }

    protected function applySearchConstraint(Builder $query, string $search): Builder
    {
        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        $searchOperator = match ($databaseConnection->getDriverName()) {
            'pgsql' => 'ilike',
            default => 'like',
        };

        $isFirst = true;

        $query->where(function (Builder $query) use ($isFirst, $searchOperator, $search): Builder {
            foreach ($this->getSearchColumns() as $searchColumnName) {
                $whereClause = $isFirst ? 'where' : 'orWhere';

                $query->{$whereClause}(
                    $searchColumnName,
                    $searchOperator,
                    "%{$search}%",
                );

                $isFirst = false;
            }

            return $query;
        });

        return $query;
    }

    public function getOptionLabelFromRecordUsing(?Closure $callback): static
    {
        $this->getOptionLabelFromRecordUsing = $callback;

        return $this;
    }

    public function hasOptionLabelFromRecordUsingCallback(): bool
    {
        return $this->getOptionLabelFromRecordUsing !== null;
    }

    public function getOptionLabelFromRecord(Model $record): string
    {
        return $this->evaluate($this->getOptionLabelFromRecordUsing, ['record' => $record]);
    }

    public function getTitleColumnName(): string
    {
        return $this->evaluate($this->titleColumnName);
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

    public function getRelationship(): BelongsTo
    {
        return $this->getModelInstance()->{$this->getRelationshipName()}();
    }

    public function getRelationshipName(): string
    {
        return $this->evaluate($this->relationship);
    }

    public function isPreloaded(): bool
    {
        return $this->evaluate($this->isPreloaded);
    }

    public function hasDynamicOptions(): bool
    {
        return $this->isPreloaded();
    }

    public function hasDynamicSearchResults(): bool
    {
        return ! $this->isPreloaded();
    }

    public function getActionFormModel(): Model | string | null
    {
        return $this->getRelationship()->getModel()::class;
    }
}
