<?php

namespace Filament\Actions;

use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Table;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Relation;

use function Filament\Support\generate_search_column_expression;
use function Filament\Support\generate_search_term_expression;

class AssociateAction extends Action
{
    use CanCustomizeProcess;

    protected ?Closure $modifyRecordSelectUsing = null;

    protected ?Closure $modifyRecordSelectOptionsQueryUsing = null;

    protected bool | Closure $canAssociateAnother = true;

    protected bool | Closure $isRecordSelectPreloaded = false;

    /**
     * @var array<string> | Closure | null
     */
    protected array | Closure | null $recordSelectSearchColumns = null;

    protected bool | Closure | null $isSearchForcedCaseInsensitive = null;

    protected bool | Closure $isMultiple = false;

    public static function getDefaultName(): ?string
    {
        return 'associate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::associate.single.label'));

        $this->modalHeading(fn (): string => __('filament-actions::associate.single.modal.heading', ['label' => $this->getTitleCaseModelLabel()]));

        $this->modalSubmitActionLabel(__('filament-actions::associate.single.modal.actions.associate.label'));

        $this->modalWidth(Width::Large);

        $this->extraModalFooterActions(function (): array {
            return $this->canAssociateAnother ? [
                $this->makeModalSubmitAction('associateAnother', arguments: ['another' => true])
                    ->label(__('filament-actions::associate.single.modal.actions.associate_another.label')),
            ] : [];
        });

        $this->successNotificationTitle(__('filament-actions::associate.single.notifications.associated.title'));

        $this->defaultColor('gray');

        $this->schema(fn (): array => [$this->getRecordSelect()]);

        $this->action(function (array $arguments, array $data, Schema $schema, Table $table): void {
            /** @var HasMany | MorphMany $relationship */
            $relationship = Relation::noConstraints(fn () => $table->getRelationship());

            $record = $relationship->getQuery()->find($data['recordId']);

            foreach (($this->isMultiple ? $record : [$record]) as $record) {
                if ($record instanceof Model) {
                    $this->record($record);
                }

                /** @var BelongsTo $inverseRelationship */
                $inverseRelationship = $table->getInverseRelationshipFor($record);

                $this->process(function () use ($inverseRelationship, $record, $relationship): void {
                    $inverseRelationship->associate($relationship->getParent());
                    $record->save();
                }, [
                    'inverseRelationship' => $inverseRelationship,
                    'relationship' => $relationship,
                ]);
            }

            if ($arguments['another'] ?? false) {
                $this->callAfter();
                $this->sendSuccessNotification();

                $this->record(null);

                $schema->fill();

                $this->halt();

                return;
            }

            $this->success();
        });
    }

    public function recordSelect(?Closure $callback): static
    {
        $this->modifyRecordSelectUsing = $callback;

        return $this;
    }

    public function recordSelectOptionsQuery(?Closure $callback): static
    {
        $this->modifyRecordSelectOptionsQueryUsing = $callback;

        return $this;
    }

    public function associateAnother(bool | Closure $condition = true): static
    {
        $this->canAssociateAnother = $condition;

        return $this;
    }

    /**
     * @deprecated Use `associateAnother()` instead.
     */
    public function disableAssociateAnother(bool | Closure $condition = true): static
    {
        $this->associateAnother(fn (AssociateAction $action): bool => ! $action->evaluate($condition));

        return $this;
    }

    public function preloadRecordSelect(bool | Closure $condition = true): static
    {
        $this->isRecordSelectPreloaded = $condition;

        return $this;
    }

    public function canAssociateAnother(): bool
    {
        return (bool) $this->evaluate($this->canAssociateAnother);
    }

    public function isRecordSelectPreloaded(): bool
    {
        return (bool) $this->evaluate($this->isRecordSelectPreloaded);
    }

    /**
     * @param  array<string> | Closure | null  $columns
     */
    public function recordSelectSearchColumns(array | Closure | null $columns): static
    {
        $this->recordSelectSearchColumns = $columns;

        return $this;
    }

    /**
     * @return array<string> | null
     */
    public function getRecordSelectSearchColumns(): ?array
    {
        return $this->evaluate($this->recordSelectSearchColumns);
    }

    public function multiple(bool | Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }

    public function getRecordSelect(): Select
    {
        $table = $this->getTable();

        $getOptions = function (int $optionsLimit, ?string $search = null, ?array $searchColumns = []) use ($table): array {
            /** @var HasMany | MorphMany $relationship */
            $relationship = Relation::noConstraints(fn () => $table->getRelationship());

            $relationshipQuery = $relationship->getQuery();

            if ($this->modifyRecordSelectOptionsQueryUsing) {
                $relationshipQuery = $this->evaluate($this->modifyRecordSelectOptionsQueryUsing, [
                    'query' => $relationshipQuery,
                    'search' => $search,
                ]) ?? $relationshipQuery;
            }

            if (! isset($relationshipQuery->getQuery()->limit)) {
                $relationshipQuery->limit($optionsLimit);
            }

            $titleAttribute = $this->getRecordTitleAttribute();
            $titleAttribute = filled($titleAttribute) ? $relationshipQuery->qualifyColumn($titleAttribute) : null;

            if (filled($search) && ($searchColumns || filled($titleAttribute))) {
                /** @var Connection $databaseConnection */
                $databaseConnection = $relationshipQuery->getConnection();

                $isForcedCaseInsensitive = $this->isSearchForcedCaseInsensitive();

                $search = generate_search_term_expression($search, $isForcedCaseInsensitive, $databaseConnection);
                $searchColumns ??= [$titleAttribute];

                $isFirst = true;

                $relationshipQuery->where(function (Builder $query) use ($databaseConnection, $isFirst, $isForcedCaseInsensitive, $searchColumns, $search): Builder {
                    foreach ($searchColumns as $searchColumn) {
                        $whereClause = $isFirst ? 'where' : 'orWhere';

                        $query->{$whereClause}(
                            generate_search_column_expression($query->qualifyColumn($searchColumn), $isForcedCaseInsensitive, $databaseConnection),
                            'like',
                            "%{$search}%",
                        );

                        $isFirst = false;
                    }

                    return $query;
                });
            }

            $relationCountHash = $relationship->getRelationCountHash(incrementJoinCount: false);

            if ($relationship instanceof MorphMany) {
                $relationshipQuery->whereNotMorphedTo($table->getInverseRelationship(), $relationship->getParent());
            } else {
                $relationshipQuery
                    ->whereDoesntHave($table->getInverseRelationship(), fn (Builder $query): Builder => $query->where(
                        // https://github.com/filamentphp/filament/issues/8067
                        $relationship->getParent()->getTable() === $relationship->getRelated()->getTable() ?
                            "{$relationCountHash}.{$relationship->getParent()->getKeyName()}" :
                            $relationship->getParent()->getQualifiedKeyName(),
                        $relationship->getParent()->getKey(),
                    ));
            }

            if (
                filled($titleAttribute) &&
                (! $this->hasCustomRecordTitle()) &&
                ($this->hasCustomRecordTitleAttribute() || (! $this->getTable()->hasCustomRecordTitle()))
            ) {
                if (empty($relationshipQuery->getQuery()->orders)) {
                    $relationshipQuery->orderBy($titleAttribute);
                }

                return $relationshipQuery
                    ->pluck($titleAttribute, $relationship->getModel()->getQualifiedKeyName())
                    ->all();
            }

            return $relationshipQuery
                ->get()
                ->mapWithKeys(fn (Model $record): array => [$record->getKey() => $this->getRecordTitle($record)])
                ->all();
        };

        $select = Select::make('recordId')
            ->label(__('filament-actions::associate.single.modal.fields.record_id.label'))
            ->required()
            ->multiple($this->isMultiple())
            ->searchable($this->getRecordSelectSearchColumns() ?? true)
            ->getSearchResultsUsing(static fn (Select $component, string $search): array => $getOptions(optionsLimit: $component->getOptionsLimit(), search: $search, searchColumns: $component->getSearchColumns()))
            ->getOptionLabelUsing(function ($value) use ($table): string {
                $relationship = Relation::noConstraints(fn () => $table->getRelationship());

                return $this->getRecordTitle($relationship->getQuery()->find($value));
            })
            ->getOptionLabelsUsing(function (array $values) use ($table): array {
                $relationship = Relation::noConstraints(fn () => $table->getRelationship());

                return $relationship->getQuery()->findMany($values)
                    ->mapWithKeys(fn (Model $record): array => [$record->getKey() => $this->getRecordTitle($record)])
                    ->all();
            })
            ->options(fn (Select $component): array => $this->isRecordSelectPreloaded() ? $getOptions(optionsLimit: $component->getOptionsLimit()) : [])
            ->hiddenLabel();

        if ($this->modifyRecordSelectUsing) {
            $select = $this->evaluate($this->modifyRecordSelectUsing, [
                'select' => $select,
            ]);
        }

        return $select;
    }

    public function forceSearchCaseInsensitive(bool | Closure | null $condition = true): static
    {
        $this->isSearchForcedCaseInsensitive = $condition;

        return $this;
    }

    public function isSearchForcedCaseInsensitive(): ?bool
    {
        return $this->evaluate($this->isSearchForcedCaseInsensitive);
    }
}
