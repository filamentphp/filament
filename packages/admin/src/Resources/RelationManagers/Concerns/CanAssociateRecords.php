<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CanAssociateRecords
{
    protected ?Form $resourceAssociateForm = null;

    protected static bool $canAssociateAnother = true;

    protected static bool $hasAssociateAction = false;

    protected function hasAssociateAction(): bool
    {
        return static::$hasAssociateAction;
    }

    protected function canAssociate(): bool
    {
        return $this->hasAssociateAction() && $this->can('associate');
    }

    protected static function canAssociateAnother(): bool
    {
        return static::$canAssociateAnother;
    }

    public static function disableAssociateAnother(): void
    {
        static::$canAssociateAnother = false;
    }

    public static function associateForm(Form $form): Form
    {
        return $form->schema([
            static::getAssociateFormRecordSelect(),
        ]);
    }

    protected function getResourceAssociateForm(): Form
    {
        if (! $this->resourceAssociateForm) {
            $this->resourceAssociateForm = static::associateForm(Form::make());
        }

        return $this->resourceAssociateForm;
    }

    protected static function getAssociateFormRecordSelect(): Select
    {
        return Select::make('recordId')
            ->label(__('filament::resources/relation-managers/associate.action.modal.fields.record_id.label'))
            ->searchable()
            ->getSearchResultsUsing(static function (Select $component, RelationManager $livewire, string $searchQuery): array {
                /** @var HasMany $relationship */
                $relationship = $livewire->getRelationship();

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
            ->disableLabel();
    }

    protected function getAssociateFormSchema(): array
    {
        return $this->getResourceAssociateForm()->getSchema();
    }

    protected function fillAssociateForm(): void
    {
        $this->callHook('beforeFill');
        $this->callHook('beforeAssociateFill');

        $this->getMountedTableActionForm()->fill();

        $this->callHook('afterFill');
        $this->callHook('afterAssociateFill');
    }

    public function associate(bool $another = false): void
    {
        $form = $this->getMountedTableActionForm();

        $this->callHook('beforeValidate');
        $this->callHook('beforeCreateValidate');

        $data = $form->getState();

        $this->callHook('afterValidate');
        $this->callHook('afterCreateValidate');

        $this->callHook('beforeAssociate');

        /** @var HasMany $relationship */
        $relationship = $this->getRelationship();

        $recordToAssociate = $relationship->getRelated()->query()->find($data['recordId']);

        /** @var BelongsTo $inverseRelationship */
        $inverseRelationship = $this->getInverseRelationshipFor($recordToAssociate);

        $inverseRelationship->associate($this->ownerRecord);
        $recordToAssociate->save();

        $this->callHook('afterAssociate');

        if ($another) {
            $form->fill();
        }

        $this->notify('success', __('filament::resources/relation-managers/associate.action.messages.associated'));
    }

    public function associateAndAssociateAnother(): void
    {
        $this->associate(another: true);
    }

    protected function getAssociateAction(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('associate')
            ->label(__('filament::resources/relation-managers/associate.action.label'))
            ->form($this->getAssociateFormSchema())
            ->mountUsing(fn () => $this->fillAssociateForm())
            ->modalSubmitAction($this->getAssociateActionAssociateModalAction())
            ->modalCancelAction($this->getAssociateActionCancelModalAction())
            ->modalActions($this->getAssociateActionModalActions())
            ->modalHeading(__('filament::resources/relation-managers/associate.action.modal.heading', ['label' => static::getRecordLabel()]))
            ->modalWidth('lg')
            ->action(fn () => $this->associate())
            ->color('secondary')
            ->button();
    }

    protected function getAssociateActionModalActions(): array
    {
        return array_merge(
            [$this->getAssociateActionAssociateModalAction()],
            static::canAssociateAnother() ? [$this->getAssociateActionAssociateAndAssociateAnotherModalAction()] : [],
            [$this->getAssociateActionCancelModalAction()],
        );
    }

    protected function getAssociateActionAssociateModalAction(): Tables\Actions\Modal\Actions\Action
    {
        return Tables\Actions\Action::makeModalAction('associate')
            ->label(__('filament::resources/relation-managers/associate.action.modal.actions.associate.label'))
            ->submit('callMountedTableAction')
            ->color('primary');
    }

    protected function getAssociateActionAssociateAndAssociateAnotherModalAction(): Tables\Actions\Modal\Actions\Action
    {
        return Tables\Actions\Action::makeModalAction('associateAndAssociateAnother')
            ->label(__('filament::resources/relation-managers/associate.action.modal.actions.associate_and_associate_another.label'))
            ->action('associateAndAssociateAnother')
            ->color('secondary');
    }

    protected function getAssociateActionCancelModalAction(): Tables\Actions\Modal\Actions\Action
    {
        return Tables\Actions\Action::makeModalAction('cancel')
            ->label(__('filament-support::actions.modal.buttons.cancel.label'))
            ->cancel()
            ->color('secondary');
    }
}
