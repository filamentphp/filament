<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Services\RelationshipJoiner;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class CheckboxList extends Field implements Contracts\CanDisableOptions, Contracts\HasNestedRecursiveValidationRules
{
    use Concerns\CanBeSearchable;
    use Concerns\CanDisableOptions;
    use Concerns\CanDisableOptionsWhenSelectedInSiblingRepeaterItems;
    use Concerns\CanFixIndistinctState;
    use Concerns\CanLimitItemsLength;
    use Concerns\HasDescriptions;
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasGridDirection;
    use Concerns\HasNestedRecursiveValidationRules;
    use Concerns\HasOptions;
    use Concerns\HasPivotData;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.checkbox-list';

    protected string | Closure | null $relationshipTitleAttribute = null;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    protected string | Closure | null $relationship = null;

    protected bool | Closure $isBulkToggleable = false;

    protected ?Closure $modifySelectAllActionUsing = null;

    protected ?Closure $modifyDeselectAllActionUsing = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);

        $this->afterStateHydrated(static function (CheckboxList $component, $state) {
            if (is_array($state)) {
                return;
            }

            $component->state([]);
        });

        $this->searchDebounce(0);

        $this->registerActions([
            fn (CheckboxList $component): Action => $component->getSelectAllAction(),
            fn (CheckboxList $component): Action => $component->getDeselectAllAction(),
        ]);
    }

    public function getSelectAllAction(): Action
    {
        $action = Action::make($this->getSelectAllActionName())
            ->label(__('filament-forms::components.checkbox_list.actions.select_all.label'))
            ->livewireClickHandlerEnabled(false)
            ->link()
            ->size(ActionSize::Small);

        if ($this->modifySelectAllActionUsing) {
            $action = $this->evaluate($this->modifySelectAllActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function selectAllAction(?Closure $callback): static
    {
        $this->modifySelectAllActionUsing = $callback;

        return $this;
    }

    public function getSelectAllActionName(): string
    {
        return 'selectAll';
    }

    public function getDeselectAllAction(): Action
    {
        $action = Action::make($this->getDeselectAllActionName())
            ->label(__('filament-forms::components.checkbox_list.actions.deselect_all.label'))
            ->livewireClickHandlerEnabled(false)
            ->link()
            ->size(ActionSize::Small);

        if ($this->modifyDeselectAllActionUsing) {
            $action = $this->evaluate($this->modifyDeselectAllActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function deselectAllAction(?Closure $callback): static
    {
        $this->modifyDeselectAllActionUsing = $callback;

        return $this;
    }

    public function getDeselectAllActionName(): string
    {
        return 'deselectAll';
    }

    public function relationship(string | Closure | null $name = null, string | Closure | null $titleAttribute = null, ?Closure $modifyQueryUsing = null): static
    {
        $this->relationship = $name ?? $this->getName();
        $this->relationshipTitleAttribute = $titleAttribute;

        $this->options(static function (CheckboxList $component) use ($modifyQueryUsing): array {
            $relationship = Relation::noConstraints(fn () => $component->getRelationship());

            $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

            if ($modifyQueryUsing) {
                $relationshipQuery = $component->evaluate($modifyQueryUsing, [
                    'query' => $relationshipQuery,
                ]) ?? $relationshipQuery;
            }

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $relationshipQuery
                    ->get()
                    ->mapWithKeys(static fn (Model $record) => [
                        $record->{Str::afterLast($relationship->getQualifiedRelatedKeyName(), '.')} => $component->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            $relationshipTitleAttribute = $component->getRelationshipTitleAttribute();

            if (empty($relationshipQuery->getQuery()->orders)) {
                $relationshipQuery->orderBy($relationshipQuery->qualifyColumn($relationshipTitleAttribute));
            }

            if (str_contains($relationshipTitleAttribute, '->')) {
                if (! str_contains($relationshipTitleAttribute, ' as ')) {
                    $relationshipTitleAttribute .= " as {$relationshipTitleAttribute}";
                }
            } else {
                $relationshipTitleAttribute = $relationshipQuery->qualifyColumn($relationshipTitleAttribute);
            }

            return $relationshipQuery
                ->pluck($relationshipTitleAttribute, $relationship->getQualifiedRelatedKeyName())
                ->toArray();
        });

        $this->loadStateFromRelationshipsUsing(static function (CheckboxList $component, ?array $state): void {
            $relationship = $component->getRelationship();

            /** @var Collection $relatedModels */
            $relatedModels = $relationship->getResults();

            $component->state(
                // Cast the related keys to a string, otherwise Livewire does not
                // know how to handle deselection.
                //
                // https://github.com/filamentphp/filament/issues/1111
                $relatedModels
                    ->pluck($relationship->getRelatedKeyName())
                    ->map(static fn ($key): string => strval($key))
                    ->toArray(),
            );
        });

        $this->saveRelationshipsUsing(static function (CheckboxList $component, ?array $state) {
            $pivotData = $component->getPivotData();

            if ($pivotData === []) {
                $component->getRelationship()->sync($state ?? []);

                return;
            }

            $component->getRelationship()->syncWithPivotValues($state ?? [], $pivotData);
        });

        $this->dehydrated(false);

        return $this;
    }

    public function bulkToggleable(bool | Closure $condition = true): static
    {
        $this->isBulkToggleable = $condition;

        return $this;
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
        return $this->evaluate(
            $this->getOptionLabelFromRecordUsing,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: [
                Model::class => $record,
                $record::class => $record,
            ],
        );
    }

    public function getRelationshipTitleAttribute(): ?string
    {
        return $this->evaluate($this->relationshipTitleAttribute);
    }

    public function getLabel(): string | Htmlable | null
    {
        if ($this->label === null && $this->getRelationship()) {
            $label = (string) str($this->getRelationshipName())
                ->before('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();

            return ($this->shouldTranslateLabel) ? __($label) : $label;
        }

        return parent::getLabel();
    }

    public function getRelationship(): ?BelongsToMany
    {
        $name = $this->getRelationshipName();

        if (blank($name)) {
            return null;
        }

        return $this->getModelInstance()->{$name}();
    }

    public function getRelationshipName(): ?string
    {
        return $this->evaluate($this->relationship);
    }

    public function isBulkToggleable(): bool
    {
        return (bool) $this->evaluate($this->isBulkToggleable);
    }
}
