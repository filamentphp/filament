<?php

namespace Filament\Forms\Components;

use Closure;
use Exception;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class Select extends Field implements Contracts\HasNestedRecursiveValidationRules
{
    use Concerns\CanAllowHtml;
    use Concerns\CanBePreloaded;
    use Concerns\CanBeSearchable;
    use Concerns\CanDisableOptions;
    use Concerns\CanDisablePlaceholderSelection;
    use Concerns\CanLimitItemsLength;
    use Concerns\HasAffixes {
        getActions as getBaseActions;
        getSuffixAction as getBaseSuffixAction;
    }
    use Concerns\HasExtraInputAttributes;
    use Concerns\HasNestedRecursiveValidationRules;
    use Concerns\HasLoadingMessage;
    use Concerns\HasOptions;
    use Concerns\HasPlaceholder;
    use HasExtraAlpineAttributes;

    protected string $view = 'forms::components.select';

    protected array | Closure | null $createOptionActionFormSchema = null;

    protected ?Closure $createOptionUsing = null;

    protected string | Closure | null $createOptionModalHeading = null;

    protected ?Closure $modifyCreateOptionActionUsing = null;

    protected bool | Closure $isMultiple = false;

    protected ?Closure $getOptionLabelUsing = null;

    protected ?Closure $getOptionLabelsUsing = null;

    protected ?Closure $getSearchResultsUsing = null;

    protected ?array $searchColumns = null;

    protected string | Closure | null $maxItemsMessage = null;

    protected string | Closure | null $position = null;

    protected string | Closure | null $relationshipTitleColumnName = null;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    protected string | Closure | null $relationship = null;

    protected int | Closure $optionsLimit = 50;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(static fn (Select $component): ?array => $component->isMultiple() ? [] : null);

        $this->afterStateHydrated(static function (Select $component, $state): void {
            if (! $component->isMultiple()) {
                return;
            }

            if (is_array($state)) {
                return;
            }

            $component->state([]);
        });

        $this->getOptionLabelUsing(static function (Select $component, $value): ?string {
            if (array_key_exists($value, $options = $component->getOptions())) {
                return $options[$value];
            }

            return $value;
        });

        $this->getOptionLabelsUsing(static function (Select $component, array $values): array {
            $options = $component->getOptions();

            $labels = [];

            foreach ($values as $value) {
                $labels[$value] = $options[$value] ?? $value;
            }

            return $labels;
        });

        $this->placeholder(__('forms::components.select.placeholder'));
    }

    public function boolean(?string $trueLabel = null, ?string $falseLabel = null, ?string $placeholder = null): static
    {
        $this->options([
            1 => $trueLabel ?? __('forms::components.select.boolean.true'),
            0 => $falseLabel ?? __('forms::components.select.boolean.false'),
        ]);

        $this->placeholder($placeholder ?? '-');

        return $this;
    }

    public function createOptionAction(?Closure $callback): static
    {
        $this->modifyCreateOptionActionUsing = $callback;

        return $this;
    }

    public function createOptionForm(array | Closure | null $schema): static
    {
        $this->createOptionActionFormSchema = $schema;

        return $this;
    }

    public function getSuffixAction(): ?Action
    {
        $action = $this->getBaseSuffixAction();

        if ($action) {
            return $action;
        }

        $createOptionAction = $this->getCreateOptionAction();

        if (! $createOptionAction) {
            return null;
        }

        return $createOptionAction;
    }

    public function getActions(): array
    {
        $actions = $this->getBaseActions();

        $createOptionActionName = $this->getCreateOptionActionName();

        if (array_key_exists($createOptionActionName, $actions)) {
            return $actions;
        }

        $createOptionAction = $this->getCreateOptionAction();

        if (! $createOptionAction) {
            return $actions;
        }

        return array_merge([$createOptionActionName => $createOptionAction], $actions);
    }

    public function createOptionUsing(Closure $callback): static
    {
        $this->createOptionUsing = $callback;

        return $this;
    }

    public function getCreateOptionUsing(): ?Closure
    {
        return $this->createOptionUsing;
    }

    protected function getCreateOptionActionName(): string
    {
        return 'createOption';
    }

    public function getCreateOptionAction(): ?Action
    {
        $actionFormSchema = $this->getCreateOptionActionFormSchema();

        if (! $actionFormSchema) {
            return null;
        }

        $action = Action::make($this->getCreateOptionActionName())
            ->component($this)
            ->form($actionFormSchema)
            ->action(static function (Select $component, array $data, ComponentContainer $form) {
                if (! $component->getCreateOptionUsing()) {
                    throw new Exception("Select field [{$component->getStatePath()}] must have a [createOptionUsing()] closure set.");
                }

                $createdOptionKey = $component->evaluate($component->getCreateOptionUsing(), [
                    'data' => $data,
                    'form' => $form,
                ]);

                $state = $component->isMultiple() ?
                    array_merge($component->getState(), [$createdOptionKey]) :
                    $createdOptionKey;

                $component->state($state);
                $component->callAfterStateUpdated();
            })
            ->icon('heroicon-o-plus')
            ->iconButton()
            ->modalHeading($this->getCreateOptionModalHeading() ?? __('forms::components.select.actions.create_option.modal.heading'))
            ->modalButton(__('forms::components.select.actions.create_option.modal.actions.create.label'))
            ->hidden(fn (Component $component): bool => $component->isDisabled());

        if ($this->modifyCreateOptionActionUsing) {
            $action = $this->evaluate($this->modifyCreateOptionActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function getCreateOptionActionFormSchema(): ?array
    {
        return $this->evaluate($this->createOptionActionFormSchema);
    }

    public function createOptionModalHeading(string | Closure | null $heading): static
    {
        $this->createOptionModalHeading = $heading;

        return $this;
    }

    public function getCreateOptionModalHeading(): ?string
    {
        return $this->evaluate($this->createOptionModalHeading);
    }

    public function getOptionLabelUsing(?Closure $callback): static
    {
        $this->getOptionLabelUsing = $callback;

        return $this;
    }

    public function getOptionLabelsUsing(?Closure $callback): static
    {
        $this->getOptionLabelsUsing = $callback;

        return $this;
    }

    public function getSearchResultsUsing(?Closure $callback): static
    {
        $this->getSearchResultsUsing = $callback;

        return $this;
    }

    public function searchable(bool | array | Closure $condition = true): static
    {
        if (is_array($condition)) {
            $this->isSearchable = true;
            $this->searchColumns = $condition;
        } else {
            $this->isSearchable = $condition;
            $this->searchColumns = null;
        }

        return $this;
    }

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function position(string | Closure | null $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function maxItemsMessage(string | Closure | null $message): static
    {
        $this->maxItemsMessage = $message;

        return $this;
    }

    public function optionsLimit(int | Closure $limit): static
    {
        $this->optionsLimit = $limit;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->evaluate($this->position);
    }

    public function getOptionLabel(): ?string
    {
        return $this->evaluate($this->getOptionLabelUsing, [
            'value' => $this->getState(),
        ]);
    }

    public function getOptionLabels(): array
    {
        $labels = $this->evaluate($this->getOptionLabelsUsing, [
            'values' => $this->getState(),
        ]);

        if ($labels instanceof Arrayable) {
            $labels = $labels->toArray();
        }

        return $labels;
    }

    public function getSearchColumns(): ?array
    {
        $columns = $this->searchColumns;

        if ($this->hasRelationship()) {
            $columns ??= [$this->getRelationshipTitleColumnName()];
        }

        return $columns;
    }

    public function getSearchResults(string $search): array
    {
        if (! $this->getSearchResultsUsing) {
            return [];
        }

        $results = $this->evaluate($this->getSearchResultsUsing, [
            'query' => $search,
            'search' => $search,
            'searchQuery' => $search,
        ]);

        if ($results instanceof Arrayable) {
            $results = $results->toArray();
        }

        return $results;
    }

    public function getSearchResultsForJs(string $search): array
    {
        return $this->transformOptionsForJs($this->getSearchResults($search));
    }

    public function getOptionsForJs(): array
    {
        return $this->transformOptionsForJs($this->getOptions());
    }

    public function getOptionLabelsForJs(): array
    {
        return $this->transformOptionsForJs($this->getOptionLabels());
    }

    protected function transformOptionsForJs(array $options): array
    {
        return collect($options)
            ->map(fn ($label, $value): array => ['label' => $label, 'value' => strval($value)])
            ->values()
            ->all();
    }

    public function isMultiple(): bool
    {
        return $this->evaluate($this->isMultiple);
    }

    public function isSearchable(): bool
    {
        return $this->evaluate($this->isSearchable) || $this->isMultiple();
    }

    public function relationship(string | Closure $relationshipName, string | Closure $titleColumnName, ?Closure $callback = null): static
    {
        $this->relationship = $relationshipName;
        $this->relationshipTitleColumnName = $titleColumnName;

        $this->getSearchResultsUsing(static function (Select $component, ?string $search) use ($callback): array {
            $relationship = $component->getRelationship();

            $relationshipQuery = $relationship->getRelated()->query();

            if ($callback) {
                $relationshipQuery = $component->evaluate($callback, [
                    'query' => $relationshipQuery,
                ]) ?? $relationshipQuery;
            }

            if (empty($relationshipQuery->getQuery()->orders)) {
                $relationshipQuery->orderBy($component->getRelationshipTitleColumnName());
            }

            $component->applySearchConstraint(
                $relationshipQuery,
                strtolower($search),
            );

            $baseRelationshipQuery = $relationshipQuery->getQuery();

            if (isset($baseRelationshipQuery->limit)) {
                $component->optionsLimit($baseRelationshipQuery->limit);
            } else {
                $relationshipQuery->limit($component->getOptionsLimit());
            }

            if ($relationship instanceof \Znck\Eloquent\Relations\BelongsToThrough) {
                $keyName = $relationship->getRelated()->getKeyName();
            } else {
                $keyName = $component->isMultiple() ? $relationship->getRelatedKeyName() : $relationship->getOwnerKeyName();
            }

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $relationshipQuery
                    ->get()
                    ->mapWithKeys(static fn (Model $record) => [
                        $record->{$keyName} => $component->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            $relationshipTitleColumnName = $component->getRelationshipTitleColumnName();

            if (
                str_contains($relationshipTitleColumnName, '->') &&
                (! str_contains($relationshipTitleColumnName, ' as '))
            ) {
                $relationshipTitleColumnName .= " as {$relationshipTitleColumnName}";
            }

            return $relationshipQuery
                ->pluck($relationshipTitleColumnName, $keyName)
                ->toArray();
        });

        $this->options(static function (Select $component) use ($callback): ?array {
            if (($component->isSearchable()) && ! $component->isPreloaded()) {
                return null;
            }

            $relationship = $component->getRelationship();

            $relationshipQuery = $relationship->getRelated()->query();

            if ($callback) {
                $relationshipQuery = $component->evaluate($callback, [
                    'query' => $relationshipQuery,
                ]) ?? $relationshipQuery;
            }

            if (empty($relationshipQuery->getQuery()->orders)) {
                $relationshipQuery->orderBy($component->getRelationshipTitleColumnName());
            }

            if ($relationship instanceof \Znck\Eloquent\Relations\BelongsToThrough) {
                $keyName = $relationship->getRelated()->getKeyName();
            } else {
                $keyName = $component->isMultiple() ? $relationship->getRelatedKeyName() : $relationship->getOwnerKeyName();
            }

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $relationshipQuery
                    ->get()
                    ->mapWithKeys(static fn (Model $record) => [
                        $record->{$keyName} => $component->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            $relationshipTitleColumnName = $component->getRelationshipTitleColumnName();

            if (
                str_contains($relationshipTitleColumnName, '->') &&
                (! str_contains($relationshipTitleColumnName, ' as '))
            ) {
                $relationshipTitleColumnName .= " as {$relationshipTitleColumnName}";
            }

            return $relationshipQuery
                ->pluck($relationshipTitleColumnName, $keyName)
                ->toArray();
        });

        $this->loadStateFromRelationshipsUsing(static function (Select $component, $state): void {
            if (filled($state)) {
                return;
            }

            $relationship = $component->getRelationship();

            if ($component->isMultiple()) {
                $relatedModels = $relationship->getResults();

                $component->state(
                    // Cast the related keys to a string, otherwise JavaScript does not
                    // know how to handle deselection.
                    //
                    // https://github.com/filamentphp/filament/issues/1111
                    $relatedModels
                        ->pluck($relationship->getRelatedKeyName())
                        ->map(static fn ($key): string => strval($key))
                        ->toArray(),
                );

                return;
            }

            /** @var BelongsTo $relationship */
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

        $this->getOptionLabelUsing(static function (Select $component, $value) use ($callback) {
            $relationship = $component->getRelationship();

            $relationshipQuery = $relationship->getRelated()->query()->where($relationship->getOwnerKeyName(), $value);

            if ($callback) {
                $relationshipQuery = $component->evaluate($callback, [
                    'query' => $relationshipQuery,
                ]) ?? $relationshipQuery;
            }

            $record = $relationshipQuery->first();

            if (! $record) {
                return null;
            }

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $component->getOptionLabelFromRecord($record);
            }

            return $record->getAttributeValue($component->getRelationshipTitleColumnName());
        });

        $this->getOptionLabelsUsing(static function (Select $component, array $values) use ($callback): array {
            $relationship = $component->getRelationship();
            $relatedKeyName = $relationship->getRelatedKeyName();

            $relationshipQuery = $relationship->getRelated()->query()
                ->whereIn($relatedKeyName, $values);

            if ($callback) {
                $relationshipQuery = $component->evaluate($callback, [
                    'query' => $relationshipQuery,
                ]) ?? $relationshipQuery;
            }

            if ($component->hasOptionLabelFromRecordUsingCallback()) {
                return $relationshipQuery
                    ->get()
                    ->mapWithKeys(static fn (Model $record) => [
                        $record->{$relatedKeyName} => $component->getOptionLabelFromRecord($record),
                    ])
                    ->toArray();
            }

            $relationshipTitleColumnName = $component->getRelationshipTitleColumnName();

            if (
                str_contains($relationshipTitleColumnName, '->') &&
                (! str_contains($relationshipTitleColumnName, ' as '))
            ) {
                $relationshipTitleColumnName .= " as {$relationshipTitleColumnName}";
            }

            return $relationshipQuery
                ->pluck($relationshipTitleColumnName, $relatedKeyName)
                ->toArray();
        });

        $this->rule(
            static function (Select $component): Exists {
                if ($component->getRelationship() instanceof \Znck\Eloquent\Relations\BelongsToThrough) {
                    $column = $component->getRelationship()->getRelated()->getKeyName();
                } else {
                    $column = $component->getRelationship()->getOwnerKeyName();
                }

                return Rule::exists(
                    $component->getRelationship()->getModel()::class,
                    $column,
                );
            },
            static fn (Select $component): bool => ! $component->isMultiple(),
        );

        $this->saveRelationshipsUsing(static function (Select $component, Model $record, $state) {
            if ($component->isMultiple()) {
                $component->getRelationship()->sync($state ?? []);

                return;
            }

            $component->getRelationship()->associate($state);
            $record->save();
        });

        $this->createOptionUsing(static function (Select $component, array $data, ComponentContainer $form) {
            $record = $component->getRelationship()->getRelated();
            $record->fill($data);
            $record->save();

            $form->model($record)->saveRelationships();

            return $record->getKey();
        });

        $this->dehydrated(fn (Select $component): bool => ! $component->isMultiple());

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

    public function getRelationshipTitleColumnName(): string
    {
        return $this->evaluate($this->relationshipTitleColumnName);
    }

    public function getLabel(): string | Htmlable | null
    {
        if ($this->label === null && $this->hasRelationship()) {
            $label = (string) Str::of($this->getRelationshipName())
                ->before('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();

            return ($this->shouldTranslateLabel) ? __($label) : $label;
        }

        return parent::getLabel();
    }

    public function getRelationship(): BelongsTo | BelongsToMany | \Znck\Eloquent\Relations\BelongsToThrough | null
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

    public function hasRelationship(): bool
    {
        return filled($this->getRelationshipName());
    }

    public function hasDynamicOptions(): bool
    {
        if ($this->hasRelationship()) {
            return $this->isPreloaded();
        }

        return $this->options instanceof Closure;
    }

    public function hasDynamicSearchResults(): bool
    {
        if ($this->hasRelationship()) {
            return ! $this->isPreloaded();
        }

        return $this->getSearchResultsUsing instanceof Closure;
    }

    public function getActionFormModel(): Model | string | null
    {
        if ($this->hasRelationship()) {
            return $this->getRelationship()->getModel()::class;
        }

        return parent::getActionFormModel();
    }

    public function getOptionsLimit(): int
    {
        return $this->evaluate($this->optionsLimit);
    }

    public function getMaxItemsMessage(): string
    {
        $maxItems = $this->getMaxItems();

        return $this->evaluate($this->maxItemsMessage) ?? trans_choice('forms::components.select.max_items_message', $maxItems, [
            ':count' => $maxItems,
        ]);
    }
}
