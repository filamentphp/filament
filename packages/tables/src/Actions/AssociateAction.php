<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;

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

    public static function getDefaultName(): ?string
    {
        return 'associate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::associate.single.label'));

        $this->modalHeading(fn (): string => __('filament-actions::associate.single.modal.heading', ['label' => $this->getModelLabel()]));

        $this->modalSubmitActionLabel(__('filament-actions::associate.single.modal.actions.associate.label'));

        $this->modalWidth('lg');

        $this->extraModalFooterActions(function (): array {
            return $this->canAssociateAnother ? [
                $this->makeModalSubmitAction('associateAnother', arguments: ['another' => true])
                    ->label(__('filament-actions::associate.single.modal.actions.associate_another.label')),
            ] : [];
        });

        $this->successNotificationTitle(__('filament-actions::associate.single.notifications.associated.title'));

        $this->color('gray');

        $this->form(fn (): array => [$this->getRecordSelect()]);

        $this->action(function (array $arguments, array $data, Form $form, Table $table): void {
            /** @var HasMany | MorphMany $relationship */
            $relationship = Relation::noConstraints(fn () => $table->getRelationship());

            $record = $relationship->getQuery()->find($data['recordId']);

            if ($record instanceof Model) {
                $this->record($record);
            }

            /** @var BelongsTo $inverseRelationship */
            $inverseRelationship = $table->getInverseRelationshipFor($record);

            $this->process(function () use ($inverseRelationship, $record, $relationship) {
                $inverseRelationship->associate($relationship->getParent());
                $record->save();
            }, [
                'inverseRelationship' => $inverseRelationship,
                'relationship' => $relationship,
            ]);

            if ($arguments['another'] ?? false) {
                $this->callAfter();
                $this->sendSuccessNotification();

                $this->record(null);

                $form->fill();

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

    public function getRecordSelect(): Select
    {
        $table = $this->getTable();

        $getOptions = function (?string $search = null, ?array $searchColumns = []) use ($table): array {
            /** @var HasMany | MorphMany $relationship */
            $relationship = Relation::noConstraints(fn () => $table->getRelationship());

            $relationshipQuery = $relationship->getQuery();

            if ($this->modifyRecordSelectOptionsQueryUsing) {
                $relationshipQuery = $this->evaluate($this->modifyRecordSelectOptionsQueryUsing, [
                    'query' => $relationshipQuery,
                ]) ?? $relationshipQuery;
            }

            $titleAttribute = $this->getRecordTitleAttribute();
            $titleAttribute = filled($titleAttribute) ? $relationshipQuery->qualifyColumn($titleAttribute) : null;

            if (filled($search) && ($searchColumns || filled($titleAttribute))) {
                $searchColumns ??= [$titleAttribute];
                $isFirst = true;
                $isForcedCaseInsensitive = $this->isSearchForcedCaseInsensitive($relationshipQuery);

                if ($isForcedCaseInsensitive) {
                    $search = Str::lower($search);
                }

                $relationshipQuery->where(function (Builder $query) use ($isFirst, $isForcedCaseInsensitive, $searchColumns, $search): Builder {
                    foreach ($searchColumns as $searchColumn) {
                        $searchColumn = $query->qualifyColumn($searchColumn);

                        $caseAwareSearchColumn = $isForcedCaseInsensitive ?
                            new Expression("lower({$searchColumn})") :
                            $searchColumn;

                        $whereClause = $isFirst ? 'where' : 'orWhere';

                        $query->{$whereClause}(
                            $caseAwareSearchColumn,
                            'like',
                            "%{$search}%",
                        );

                        $isFirst = false;
                    }

                    return $query;
                });
            }

            $relationshipQuery
                ->whereDoesntHave($table->getInverseRelationship(), function (Builder $query) use ($relationship): Builder {
                    if ($relationship instanceof MorphMany) {
                        return $query
                            ->where(
                                $relationship->getMorphType(),
                                $relationship->getMorphClass(),
                            )
                            ->where(
                                $relationship->getQualifiedForeignKeyName(),
                                $relationship->getParent()->getKey(),
                            );
                    }

                    return $query->where(
                        $relationship->getParent()->getQualifiedKeyName(),
                        $relationship->getParent()->getKey(),
                    );
                });

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
            ->searchable($this->getRecordSelectSearchColumns() ?? true)
            ->getSearchResultsUsing(static fn (Select $component, string $search): array => $getOptions(search: $search, searchColumns: $component->getSearchColumns()))
            ->getOptionLabelUsing(fn ($value): string => $this->getRecordTitle(Relation::noConstraints(fn () => $table->getRelationship())->getQuery()->find($value)))
            ->options(fn (): array => $this->isRecordSelectPreloaded() ? $getOptions() : [])
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

    public function isSearchForcedCaseInsensitive(Builder $query): bool
    {
        /** @var Connection $databaseConnection */
        $databaseConnection = $query->getConnection();

        return $this->evaluate($this->isSearchForcedCaseInsensitive) ?? match ($databaseConnection->getDriverName()) {
            'pgsql' => true,
            default => false,
        };
    }
}
