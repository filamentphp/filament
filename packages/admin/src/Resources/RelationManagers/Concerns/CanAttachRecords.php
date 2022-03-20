<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Modal\Actions\ButtonAction;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

trait CanAttachRecords
{
    protected ?Form $resourceAttachForm = null;

    protected static bool $canAttachAnother = true;

    protected function canAttach(): bool
    {
        return $this->can('attach');
    }

    protected static function canAttachAnother(): bool
    {
        return static::$canAttachAnother;
    }

    public static function disableAttachAnother(): void
    {
        static::$canAttachAnother = false;
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
            $this->resourceAttachForm = static::attachForm(Form::make());
        }

        return $this->resourceAttachForm;
    }

    protected static function getAttachFormRecordSelect(): Select
    {
        return Select::make('recordId')
            ->label(__('filament::resources/relation-managers/attach.action.modal.fields.record_id.label'))
            ->searchable()
            ->getSearchResultsUsing(function (Select $component, RelationManager $livewire, string $query): array {
                $relationship = $livewire->getRelationship();

                $displayColumnName = static::getRecordTitleAttribute();

                /** @var Builder $relationshipQuery */
                $relationshipQuery = $relationship->getRelated()->query()->orderBy($displayColumnName);

                $query = strtolower($query);

                /** @var Connection $databaseConnection */
                $databaseConnection = $relationshipQuery->getConnection();

                $searchOperator = match ($databaseConnection->getDriverName()) {
                    'pgsql' => 'ilike',
                    default => 'like',
                };

                $searchColumns = $component->getSearchColumns() ?? [$displayColumnName];
                $isFirst = true;

                foreach ($searchColumns as $searchColumnName) {
                    $whereClause = $isFirst ? 'where' : 'orWhere';

                    $relationshipQuery->{$whereClause}(
                        $searchColumnName,
                        $searchOperator,
                        "%{$query}%",
                    );

                    $isFirst = false;
                }

                return $relationshipQuery
                    ->whereDoesntHave($livewire->getInverseRelationshipName(), function (Builder $query) use ($livewire): void {
                        $query->where($livewire->ownerRecord->getQualifiedKeyName(), $livewire->ownerRecord->getKey());
                    })
                    ->pluck($displayColumnName, $relationship->getRelated()->getKeyName())
                    ->toArray();
            })
            ->getOptionLabelUsing(fn (RelationManager $livewire, $value): ?string => static::getRecordTitle($livewire->getRelationship()->getRelated()->query()->find($value)))
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

        $this->callHook('beforeAttach');

        /** @var BelongsToMany $relationship */
        $relationship = $this->getRelationship();

        $pivotColumns = $relationship->getPivotColumns();

        $record = $relationship->getRelated()->query()->find($data['recordId']);
        $relationship->attach($record, Arr::only($data, $pivotColumns));

        $this->callHook('afterAttach');

        if ($another) {
            $form->fill();
        }

        if (filled($this->getAttachedNotificationMessage())) {
            $this->notify('success', $this->getAttachedNotificationMessage());
        }
    }

    protected function getAttachedNotificationMessage(): ?string
    {
        return __('filament::resources/relation-managers/attach.action.messages.attached');
    }

    public function attachAndAttachAnother(): void
    {
        $this->attach(another: true);
    }

    protected function getAttachAction(): Tables\Actions\ButtonAction
    {
        return Tables\Actions\ButtonAction::make('attach')
            ->label(__('filament::resources/relation-managers/attach.action.label'))
            ->form($this->getAttachFormSchema())
            ->mountUsing(fn () => $this->fillAttachForm())
            ->modalActions($this->getAttachActionModalActions())
            ->modalHeading(__('filament::resources/relation-managers/attach.action.modal.heading', ['label' => static::getRecordLabel()]))
            ->modalWidth('lg')
            ->action(fn () => $this->attach())
            ->color('secondary');
    }

    protected function getAttachActionModalActions(): array
    {
        return array_merge(
            [$this->getAttachActionAttachModalAction()],
            static::canAttachAnother() ? [$this->getAttachActionAttachAndAttachAnotherModalAction()] : [],
            [$this->getAttachActionCancelModalAction()],
        );
    }

    protected function getAttachActionAttachModalAction(): Tables\Actions\Modal\Actions\Action
    {
        return ButtonAction::make('attach')
            ->label(__('filament::resources/relation-managers/attach.action.modal.actions.attach.label'))
            ->submit('callMountedTableAction')
            ->color('primary');
    }

    protected function getAttachActionAttachAndAttachAnotherModalAction(): Tables\Actions\Modal\Actions\Action
    {
        return ButtonAction::make('attachAndAttachAnother')
            ->label(__('filament::resources/relation-managers/attach.action.modal.actions.attach_and_attach_another.label'))
            ->action('attachAndAttachAnother')
            ->color('secondary');
    }

    protected function getAttachActionCancelModalAction(): Tables\Actions\Modal\Actions\Action
    {
        return ButtonAction::make('cancel')
            ->label(__('tables::table.actions.modal.buttons.cancel.label'))
            ->cancel()
            ->color('secondary');
    }
}
