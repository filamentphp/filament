<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Modal\Actions\ButtonAction;
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

    public function attach(bool $another = false): void
    {
        $form = $this->getMountedTableActionForm();

        $this->callHook('beforeValidate');
        $this->callHook('beforeCreateValidate');

        $data = $form->getState();

        $this->callHook('afterValidate');
        $this->callHook('afterCreateValidate');

        $this->callHook('beforeCreate');

        $relationship = $this->getRelationship();
        $pivotColumns = $relationship->getPivotColumns();

        $record = $relationship->getRelated()->find($data['recordId']);
        $relationship->attach($record, Arr::only($data, $pivotColumns));

        $this->callHook('afterCreate');

        if ($another) {
            $form->fill();
        }
    }

    protected function getAttachButtonTableHeaderAction(): Tables\Actions\ButtonAction
    {
        return Tables\Actions\ButtonAction::make('attach')
            ->label(__('filament::resources/relation-managers/attach.action.label'))
            ->form($this->getAttachFormSchema())
            ->mountUsing(fn () => $this->fillAttachForm())
            ->modalActions([
                ButtonAction::make('submit')
                    ->label(__('filament::resources/relation-managers/attach.action.modal.actions.attach.label'))
                    ->submit()
                    ->color('primary'),
                ButtonAction::make('submit')
                    ->label(__('filament::resources/relation-managers/attach.action.modal.actions.attach_and_attach_another.label'))
                    ->action('attach(true)')
                    ->color('secondary'),
                ButtonAction::make('cancel')
                    ->label(__('tables::table.actions.modal.buttons.cancel.label'))
                    ->cancel()
                    ->color('secondary'),
            ])
            ->modalHeading(__('filament::resources/relation-managers/attach.action.modal.heading', ['label' => static::getRecordLabel()]))
            ->modalWidth('lg')
            ->action(fn () => $this->attach())
            ->color('secondary');
    }
}
