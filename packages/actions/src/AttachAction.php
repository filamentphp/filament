<?php

namespace Filament\Actions;

use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Services\RelationshipJoiner;
use Filament\Tables\Table;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;

use function Filament\Support\generate_search_column_expression;
use function Filament\Support\generate_search_term_expression;

class AttachAction extends Action
{
    use CanCustomizeProcess;

    protected ?Closure $modifyRecordSelectUsing = null;

    protected ?Closure $modifyRecordSelectOptionsQueryUsing = null;

    protected bool | Closure $canAttachAnother = true;

    protected bool | Closure $isRecordSelectPreloaded = false;

    protected bool | Closure $isMultiple = false;

    /**
     * @var array<string> | Closure | null
     */
    protected array | Closure | null $recordSelectSearchColumns = null;

    protected bool | Closure | null $isSearchForcedCaseInsensitive = null;

    public static function getDefaultName(): ?string
    {
        return 'attach';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::attach.single.label'));

        $this->modalHeading(fn (): string => __('filament-actions::attach.single.modal.heading', ['label' => $this->getTitleCaseModelLabel()]));

        $this->modalSubmitActionLabel(__('filament-actions::attach.single.modal.actions.attach.label'));

        $this->modalWidth(Width::Large);

        $this->extraModalFooterActions(function (): array {
            return $this->canAttachAnother() ? [
                $this->makeModalSubmitAction('attachAnother', ['another' => true])
                    ->label(__('filament-actions::attach.single.modal.actions.attach_another.label')),
            ] : [];
        });

        $this->successNotificationTitle(__('filament-actions::attach.single.notifications.attached.title'));

        $this->defaultColor('gray');

        $this->schema(fn (): array => [$this->getRecordSelect()]);

        $this->action(function (array $arguments, array $data, Schema $schema, Table $table): void {
            /** @var BelongsToMany $relationship */
            $relationship = Relation::noConstraints(fn () => $table->getRelationship());

            $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

            $isMultiple = is_array($data['recordId']);

            $record = $relationshipQuery
                ->{$isMultiple ? 'whereIn' : 'where'}($relationship->getQualifiedRelatedKeyName(), $data['recordId'])
                ->{$isMultiple ? 'get' : 'first'}();

            if ($record instanceof Model) {
                $this->record($record);
            }

            $this->process(function () use ($data, $record, $relationship): void {
                $relationship->attach(
                    $record,
                    Arr::only($data, $relationship->getPivotColumns()),
                );
            }, [
                'relationship' => $relationship,
            ]);

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

    public function attachAnother(bool | Closure $condition = true): static
    {
        $this->canAttachAnother = $condition;

        return $this;
    }

    /**
     * @deprecated Use `attachAnother()` instead.
     */
    public function disableAttachAnother(bool | Closure $condition = true): static
    {
        $this->attachAnother(fn (AttachAction $action): bool => ! $action->evaluate($condition));

        return $this;
    }

    public function preloadRecordSelect(bool | Closure $condition = true): static
    {
        $this->isRecordSelectPreloaded = $condition;

        return $this;
    }

    public function canAttachAnother(): bool
    {
        return (bool) $this->evaluate($this->canAttachAnother);
    }

    public function isRecordSelectPreloaded(): bool
    {
        return (bool) $this->evaluate($this->isRecordSelectPreloaded);
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

    public function getRecordSelect(): Select
    {
        $table = $this->getTable();

        $getOptions = function (int $optionsLimit, ?string $search = null, ?array $searchColumns = []) use ($table): array {
            /** @var BelongsToMany $relationship */
            $relationship = Relation::noConstraints(fn () => $table->getRelationship());

            $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

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

            $relationshipQuery
                ->when(
                    ! $table->allowsDuplicates(),
                    fn (Builder $query): Builder => $query->whereDoesntHave(
                        $table->getInverseRelationship(),
                        fn (Builder $query): Builder => $query->where(
                            // https://github.com/filamentphp/filament/issues/8067
                            $relationship->getParent()->getTable() === $relationship->getRelated()->getTable() ?
                                "{$relationCountHash}.{$relationship->getParent()->getKeyName()}" :
                                $relationship->getParent()->getQualifiedKeyName(),
                            $relationship->getParent()->getKey(),
                        ),
                    ),
                );

            if (
                filled($titleAttribute) &&
                (! $this->hasCustomRecordTitle()) &&
                ($this->hasCustomRecordTitleAttribute() || (! $this->getTable()->hasCustomRecordTitle()))
            ) {
                if (empty($relationshipQuery->getQuery()->orders)) {
                    $relationshipQuery->orderBy($titleAttribute);
                }

                return $relationshipQuery
                    ->pluck($titleAttribute, $relationship->getQualifiedRelatedKeyName())
                    ->all();
            }

            $relatedKeyName = $relationship->getRelatedKeyName();

            return $relationshipQuery
                ->get()
                ->mapWithKeys(fn (Model $record): array => [$record->{$relatedKeyName} => $this->getRecordTitle($record)])
                ->all();
        };

        $select = Select::make('recordId')
            ->label(__('filament-actions::attach.single.modal.fields.record_id.label'))
            ->required()
            ->multiple($this->isMultiple())
            ->searchable($this->getRecordSelectSearchColumns() ?? true)
            ->getSearchResultsUsing(static fn (Select $component, string $search): array => $getOptions(optionsLimit: $component->getOptionsLimit(), search: $search, searchColumns: $component->getSearchColumns()))
            ->getOptionLabelUsing(function ($value) use ($table): string {
                $relationship = Relation::noConstraints(fn () => $table->getRelationship());

                $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

                return $this->getRecordTitle($relationshipQuery->find($value));
            })
            ->getOptionLabelsUsing(function (array $values) use ($table): array {
                $relationship = Relation::noConstraints(fn () => $table->getRelationship());

                $relationshipQuery = app(RelationshipJoiner::class)->prepareQueryForNoConstraints($relationship);

                return $relationshipQuery->find($values)
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
