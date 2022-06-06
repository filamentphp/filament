<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssociateAction extends Action
{
    use Concerns\InteractsWithRelationship;

    protected ?Closure $modifyRecordSelectUsing = null;

    public static function make(string $name = 'associate'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/associate.single.label'));

        $this->modalHeading(fn (AssociateAction $action): string => __('filament-support::actions/associate.single.modal.heading', ['label' => $action->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/associate.single.modal.actions.associate.label'));

        $this->successNotificationMessage(__('filament-support::actions/associate.single.messages.associated'));

        $this->action(static function (AssociateAction $action): void {
            //

            $action->success();
        });
    }

    public function recordSelect(?Closure $callback): static
    {
        $this->modifyRecordSelectUsing = $callback;

        return $this;
    }

    public function getRecordSelect(): Select
    {
        $select = Select::make('recordId')
            ->label(__('filament-support::actions/associate.single.modal.fields.record_id.label'))
            ->searchable()
            ->getSearchResultsUsing(function (Select $component, RelationManager $livewire, string $searchQuery): array {
                /** @var HasMany $relationship */
                $relationship = $this->getRelationship();

                $displayColumnName = static::getRecordTitleAttribute();

                /** @var Builder $relationshipQuery */
                $relationshipQuery = $relationship->getRelated()->query()->orderBy($displayColumnName);

                $searchQuery = strtolower($searchQuery);

                /** @var Connection $databaseConnection */
                $databaseConnection = $relationshipQuery->getConnection();

                $searchOperator = match ($databaseConnection->getDriverName()) {
                    'pgsql' => 'ilike',
                    default => 'like',
                };

                $searchColumns = $component->getSearchColumns() ?? [$displayColumnName];
                $isFirst = true;

                $relationshipQuery->where(function (Builder $query) use ($isFirst, $searchColumns, $searchOperator, $searchQuery): Builder {
                    foreach ($searchColumns as $searchColumnName) {
                        $whereClause = $isFirst ? 'where' : 'orWhere';

                        $query->{$whereClause}(
                            $searchColumnName,
                            $searchOperator,
                            "%{$searchQuery}%",
                        );

                        $isFirst = false;
                    }

                    return $query;
                });

                $localKeyName = $relationship->getLocalKeyName();

                return $relationshipQuery
                    ->whereDoesntHave($livewire->getInverseRelationshipName(), function (Builder $query) use ($livewire): Builder {
                        return $query->where($livewire->ownerRecord->getQualifiedKeyName(), $livewire->ownerRecord->getKey());
                    })
                    ->get()
                    ->mapWithKeys(static fn (Model $record): array => [$record->{$localKeyName} => static::getRecordTitle($record)])
                    ->toArray();
            })
            ->getOptionLabelUsing(static fn (RelationManager $livewire, $value): ?string => static::getRecordTitle($livewire->getRelationship()->getRelated()->query()->find($value)))
            ->options(function (RelationManager $livewire): array {
                if (! static::$shouldPreloadAssociateFormRecordSelectOptions) {
                    return [];
                }

                /** @var HasMany $relationship */
                $relationship = $this->getRelationship();

                $displayColumnName = static::getRecordTitleAttribute();

                $localKeyName = $relationship->getLocalKeyName();

                return $relationship
                    ->getRelated()
                    ->query()
                    ->orderBy($displayColumnName)
                    ->whereDoesntHave($livewire->getInverseRelationshipName(), function (Builder $query) use ($relationship): Builder {
                        return $query->where($relationship->getParent()->getQualifiedKeyName(), $relationship->getParent()->getKey());
                    })
                    ->get()
                    ->mapWithKeys(static fn (Model $record): array => [$record->{$localKeyName} => static::getRecordTitle($record)])
                    ->toArray();
            })
            ->disableLabel();

        if ($this->modifyRecordSelectUsing) {
            $select = $this->evaluate($this->modifyRecordSelectUsing, [
                'select' => $select,
            ]);
        }

        return $select;
    }
}
