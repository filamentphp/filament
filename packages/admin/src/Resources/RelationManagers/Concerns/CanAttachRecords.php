<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

trait CanAttachRecords
{
    protected ?Form $resourceAttachForm = null;

    protected function canAttach(): bool
    {
        return $this->can('attach');
    }

    public static function attachForm(Form $form): Form
    {
        return $form->schema([
            static::getAttachFormRecordSelect(),
        ]);
    }

    protected function getResourceAttachForm(): Form
    {
        if (! $this->resourceAttachForm) {
            $this->resourceAttachForm = static::attachForm(Form::make($this));
        }

        return $this->resourceAttachForm;
    }

    protected static function getAttachFormRecordSelect(): Select
    {
        return Select::make('recordId')
            ->label(__('filament::resources/relation-managers/attach.action.modal.fields.record_id.label'))
            ->searchable()
            ->getSearchResultsUsing(function (RelationManager $livewire, string $query): array {
                $relationship = $livewire->getRelationship();

                $displayColumnName = static::getRecordTitleAttribute();

                $relationshipQuery = $relationship->getRelated()->orderBy($displayColumnName);

                $query = strtolower($query);
                $searchOperator = match ($relationshipQuery->getConnection()->getDriverName()) {
                    'pgsql' => 'ilike',
                    default => 'like',
                };

                return $relationshipQuery
                    ->where($displayColumnName, $searchOperator, "%{$query}%")
                    ->whereDoesntHave($livewire->getInverseRelationshipName(), function (Builder $query) use ($livewire): void {
                        $query->where($livewire->ownerRecord->getQualifiedKeyName(), $livewire->ownerRecord->getKey());
                    })
                    ->pluck($displayColumnName, $relationship->getRelated()->getKeyName())
                    ->toArray();
            })
            ->getOptionLabelUsing(fn (RelationManager $livewire, $value): ?string => static::getRecordTitle($livewire->getRelationship()->getRelated()->find($value)))
            ->disableLabel();
    }

    protected function getAttachFormSchema(): array
    {
        return $this->getResourceAttachForm()->getSchema();
    }

    protected function fillAttachForm(): void
    {
        $this->callHook('beforeFill');
        $this->callHook('beforeAttachFill');

        $this->getMountedTableActionForm()->fill();

        $this->callHook('afterFill');
        $this->callHook('afterAttachFill');
    }

    protected function attach(): void
    {
        $this->callHook('beforeValidate');
        $this->callHook('beforeCreateValidate');

        $data = $this->getMountedTableActionForm()->getState();

        $this->callHook('afterValidate');
        $this->callHook('afterCreateValidate');

        $this->callHook('beforeCreate');

        $relationship = $this->getRelationship();
        $pivotColumns = $relationship->getPivotColumns();

        $record = $relationship->getRelated()->find($data['recordId']);
        $relationship->attach($record, Arr::only($data, $pivotColumns));

        $this->callHook('afterCreate');
    }

    protected function getAttachButtonTableHeaderAction(): Tables\Actions\ButtonAction
    {
        return Tables\Actions\ButtonAction::make('attach')
            ->label(__('filament::resources/relation-managers/attach.action.label'))
            ->form($this->getAttachFormSchema())
            ->mountUsing(fn () => $this->fillAttachForm())
            ->modalButton(__('filament::resources/relation-managers/attach.action.modal.actions.attach.label'))
            ->modalHeading(__('filament::resources/relation-managers/attach.action.modal.heading', ['label' => static::getRecordLabel()]))
            ->modalWidth('sm')
            ->action(fn () => $this->attach())
            ->color('secondary');
    }
}
