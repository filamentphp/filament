<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Select;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssociateAction extends Action
{
    use Concerns\InteractsWithRelationship;

    protected ?Closure $modifyRecordSelectUsing = null;

    protected bool | Closure $isAssociateAnotherDisabled = false;

    protected bool | Closure $isRecordSelectPreloaded = false;

    public static function make(string $name = 'associate'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-support::actions/associate.single.label'));

        $this->modalHeading(static fn (AssociateAction $action): string => __('filament-support::actions/associate.single.modal.heading', ['label' => $action->getModelLabel()]));

        $this->modalButton(__('filament-support::actions/associate.single.modal.actions.associate.label'));

        $this->modalWidth('lg');

        $this->extraModalActions([
            $this->makeExtraModalAction('andAssociateAnother', ['another' => true])
                ->label(__('filament-support::actions/associate.single.modal.actions.associate_and_associate_another.label')),
        ]);

        $this->successNotificationMessage(__('filament-support::actions/associate.single.messages.associated'));

        $this->color('secondary');

        $this->button();

        $this->form(static fn (AssociateAction $action): array => [$action->getRecordSelect()]);

        $this->action(static function (AssociateAction $action, array $arguments, array $data, ComponentContainer $form): void {
            /** @var HasMany $relationship */
            $relationship = $action->getRelationship();

            $recordToAssociate = $relationship->getRelated()->query()->find($data['recordId']);

            /** @var BelongsTo $inverseRelationship */
            $inverseRelationship = $action->getInverseRelationshipFor($recordToAssociate);

            $inverseRelationship->associate($relationship->getParent());
            $recordToAssociate->save();

            if ($arguments['another'] ?? false) {
                $form->fill();

                $action->sendSuccessNotification();
                $action->callAfter();
                $action->hold();

                return;
            }

            $action->success();
        });
    }

    public function recordSelect(?Closure $callback): static
    {
        $this->modifyRecordSelectUsing = $callback;

        return $this;
    }

    public function disableAssociateAnother(bool | Closure $condition = true): static
    {
        $this->isAssociateAnotherDisabled = $condition;

        return $this;
    }

    public function preloadRecordSelect(bool | Closure $condition = true): static
    {
        $this->isRecordSelectPreloaded = $condition;

        return $this;
    }

    public function isAssociateAnotherDisabled(): bool
    {
        return $this->evaluate($this->isAssociateAnotherDisabled);
    }

    public function isRecordSelectPreloaded(): bool
    {
        return $this->evaluate($this->isRecordSelectPreloaded);
    }

    public function getRecordSelect(): Select
    {
        $getOptions = function (?string $search = null, ?array $searchColumns = []): array {
            /** @var HasMany $relationship */
            $relationship = $this->getRelationship();

            $displayColumnName = $this->getRecordTitleAttribute();

            $relationshipQuery = $relationship->getRelated()->query()->orderBy($displayColumnName);

            if (filled($search)) {
                $search = strtolower($search);

                /** @var Connection $databaseConnection */
                $databaseConnection = $relationshipQuery->getConnection();

                $searchOperator = match ($databaseConnection->getDriverName()) {
                    'pgsql' => 'ilike',
                    default => 'like',
                };

                $searchColumns ??= [$displayColumnName];
                $isFirst = true;

                $relationshipQuery->where(function (Builder $query) use ($isFirst, $searchColumns, $searchOperator, $search): Builder {
                    foreach ($searchColumns as $searchColumnName) {
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
            }

            $localKeyName = $relationship->getLocalKeyName();

            return $relationshipQuery
                ->whereDoesntHave($this->getInverseRelationshipName(), function (Builder $query) use ($relationship): Builder {
                    return $query->where($relationship->getParent()->getQualifiedKeyName(), $relationship->getParent()->getKey());
                })
                ->get()
                ->mapWithKeys(fn (Model $record): array => [$record->{$localKeyName} => $this->getRecordTitle($record)])
                ->toArray();
        };

        $select = Select::make('recordId')
            ->label(__('filament-support::actions/associate.single.modal.fields.record_id.label'))
            ->searchable()
            ->getSearchResultsUsing(static fn (Select $component, string $searchQuery): array => $getOptions(search: $searchQuery, searchColumns: $component->getSearchColumns()))
            ->getOptionLabelUsing(fn ($value): ?string => $this->getRecordTitle($this->getRelationship()->getRelated()->query()->find($value)))
            ->options(fn (): array => $this->isRecordSelectPreloaded() ? $getOptions() : [])
            ->disableLabel();

        if ($this->modifyRecordSelectUsing) {
            $select = $this->evaluate($this->modifyRecordSelectUsing, [
                'select' => $select,
            ]);
        }

        return $select;
    }
}
