<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
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

    /**
     * @deprecated Use `->disableAssociateAnother()` on the action instead.
     */
    protected static bool $canAssociateAnother = true;

    /**
     * @deprecated Use `->preloadRecordSelect()` on the action instead.
     */
    protected static bool $shouldPreloadAssociateFormRecordSelectOptions = false;

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected static bool $hasAssociateAction = false;

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function hasAssociateAction(): bool
    {
        return static::$hasAssociateAction;
    }

    protected function canAssociate(): bool
    {
        return $this->hasAssociateAction() && $this->can('associate');
    }

    /**
     * @deprecated Use `->disableAssociateAnother()` on the action instead.
     */
    protected static function canAssociateAnother(): bool
    {
        return static::$canAssociateAnother;
    }

    /**
     * @deprecated Use `->disableAssociateAnother()` on the action instead.
     */
    public static function disableAssociateAnother(): void
    {
        static::$canAssociateAnother = false;
    }

    /**
     * @deprecated Use `->form()` on the action instead.
     */
    public static function associateForm(Form $form): Form
    {
        return $form->schema([
            static::getAssociateFormRecordSelect(),
        ]);
    }

    /**
     * @deprecated Use `->form()` on the action instead.
     */
    protected function getResourceAssociateForm(): Form
    {
        if (! $this->resourceAssociateForm) {
            $this->resourceAssociateForm = static::associateForm(Form::make());
        }

        return $this->resourceAssociateForm;
    }

    /**
     * @deprecated Use `->recordSelect()` on the action instead.
     */
    protected static function getAssociateFormRecordSelect(): Select
    {
        return Select::make('recordId')
            ->label(__('filament-support::actions/associate.single.modal.fields.record_id.label'))
            ->required()
            ->searchable()
            ->getSearchResultsUsing(static function (Select $component, RelationManager $livewire, string $search): array {
                /** @var HasMany $relationship */
                $relationship = $livewire->getRelationship();

                $titleColumnName = static::getRecordTitleAttribute();

                /** @var Builder $relationshipQuery */
                $relationshipQuery = $relationship->getRelated()->query()->orderBy($titleColumnName);

                $search = strtolower($search);

                /** @var Connection $databaseConnection */
                $databaseConnection = $relationshipQuery->getConnection();

                $searchOperator = match ($databaseConnection->getDriverName()) {
                    'pgsql' => 'ilike',
                    default => 'like',
                };

                $searchColumns = $component->getSearchColumns() ?? [$titleColumnName];
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

                $localKeyName = $relationship->getLocalKeyName();

                return $relationshipQuery
                    ->whereDoesntHave($livewire->getInverseRelationshipName(), function (Builder $query) use ($livewire): Builder {
                        return $query->where($livewire->getOwnerRecord()->getQualifiedKeyName(), $livewire->getOwnerRecord()->getKey());
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
                $relationship = $livewire->getRelationship();

                $titleColumnName = static::getRecordTitleAttribute();

                $localKeyName = $relationship->getLocalKeyName();

                return $relationship
                    ->getRelated()
                    ->query()
                    ->orderBy($titleColumnName)
                    ->whereDoesntHave($livewire->getInverseRelationshipName(), function (Builder $query) use ($livewire): Builder {
                        return $query->where($livewire->getOwnerRecord()->getQualifiedKeyName(), $livewire->getOwnerRecord()->getKey());
                    })
                    ->get()
                    ->mapWithKeys(static fn (Model $record): array => [$record->{$localKeyName} => static::getRecordTitle($record)])
                    ->toArray();
            })
            ->disableLabel();
    }

    /**
     * @deprecated Use `->form()` on the action instead.
     */
    protected function getAssociateFormSchema(): array
    {
        return $this->getResourceAssociateForm()->getSchema();
    }

    /**
     * @deprecated Use `->mountUsing()` on the action instead.
     */
    protected function fillAssociateForm(): void
    {
        $this->callHook('beforeFill');
        $this->callHook('beforeAssociateFill');

        $this->getMountedTableActionForm()->fill();

        $this->callHook('afterFill');
        $this->callHook('afterAssociateFill');
    }

    /**
     * @deprecated Use `->action()` on the action instead.
     */
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

        $inverseRelationship->associate($this->getOwnerRecord());
        $recordToAssociate->save();

        $this->callHook('afterAssociate');

        if ($another) {
            $form->fill();
        }

        Notification::make()
            ->title(__('filament-support::actions/associate.single.messages.associated'))
            ->success()
            ->send();

        if ($another) {
            $this->getMountedTableAction()->hold();
        }
    }

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function getAssociateAction(): Tables\Actions\Action
    {
        return Tables\Actions\AssociateAction::make()
            ->form($this->getAssociateFormSchema())
            ->mountUsing(fn () => $this->fillAssociateForm())
            ->action(fn (array $arguments) => $this->associate($arguments['another'] ?? false));
    }
}
