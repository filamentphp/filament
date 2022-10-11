<?php

namespace Filament\Resources\RelationManagers\Concerns;

use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\BelongsToManyRelationManager;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

trait CanAttachRecords
{
    protected ?Form $resourceAttachForm = null;

    /**
     * @deprecated Use `->disableAttachAnother()` on the action instead.
     */
    protected static bool $canAttachAnother = true;

    /**
     * @deprecated Use `->preloadRecordSelect()` on the action instead.
     */
    protected static bool $shouldPreloadAttachFormRecordSelectOptions = false;

    /**
     * @deprecated Use `->disableAttachAnother()` on the action instead.
     */
    protected static function canAttachAnother(): bool
    {
        return static::$canAttachAnother;
    }

    /**
     * @deprecated Use `->disableAttachAnother()` on the action instead.
     */
    public static function disableAttachAnother(): void
    {
        static::$canAttachAnother = false;
    }

    /**
     * @deprecated Use `->form()` on the action instead.
     */
    public static function attachForm(Form $form): Form
    {
        return $form->schema([
            static::getAttachFormRecordSelect(),
        ]);
    }

    /**
     * @deprecated Use `->form()` on the action instead.
     */
    protected function getResourceAttachForm(): Form
    {
        if (! $this->resourceAttachForm) {
            $this->resourceAttachForm = static::attachForm(Form::make());
        }

        return $this->resourceAttachForm;
    }

    /**
     * @deprecated Use `->recordSelect()` on the action instead.
     */
    protected static function getAttachFormRecordSelect(): Select
    {
        return Select::make('recordId')
            ->label(__('filament-support::actions/attach.single.modal.fields.record_id.label'))
            ->required()
            ->searchable()
            ->getSearchResultsUsing(static function (Select $component, BelongsToManyRelationManager $livewire, string $search): array {
                /** @var BelongsToMany $relationship */
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

                $relationshipQuery->where(function (Builder $query) use ($isFirst, $search, $searchColumns, $searchOperator): Builder {
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

                $relatedKeyName = $relationship->getRelatedKeyName();

                return $relationshipQuery
                    ->when(
                        ! $livewire->allowsDuplicates(),
                        static fn (Builder $query): Builder => $query->whereDoesntHave(
                            $livewire->getInverseRelationshipName(),
                            static function (Builder $query) use ($livewire): Builder {
                                return $query->where($livewire->getOwnerRecord()->getQualifiedKeyName(), $livewire->getOwnerRecord()->getKey());
                            },
                        ),
                    )
                    ->get()
                    ->mapWithKeys(static fn (Model $record): array => [$record->{$relatedKeyName} => static::getRecordTitle($record)])
                    ->toArray();
            })
            ->getOptionLabelUsing(static fn (RelationManager $livewire, $value): ?string => static::getRecordTitle($livewire->getRelationship()->getRelated()->query()->find($value)))
            ->options(function (BelongsToManyRelationManager $livewire): array {
                if (! static::$shouldPreloadAttachFormRecordSelectOptions) {
                    return [];
                }

                /** @var BelongsToMany $relationship */
                $relationship = $livewire->getRelationship();

                $titleColumnName = static::getRecordTitleAttribute();

                $relatedKeyName = $relationship->getRelatedKeyName();

                return $relationship
                    ->getRelated()
                    ->query()
                    ->orderBy($titleColumnName)
                    ->when(
                        ! $livewire->allowsDuplicates(),
                        static fn (Builder $query): Builder => $query->whereDoesntHave(
                            $livewire->getInverseRelationshipName(),
                            static function (Builder $query) use ($livewire): Builder {
                                return $query->where($livewire->getOwnerRecord()->getQualifiedKeyName(), $livewire->getOwnerRecord()->getKey());
                            },
                        ),
                    )
                    ->get()
                    ->mapWithKeys(static fn (Model $record): array => [$record->{$relatedKeyName} => static::getRecordTitle($record)])
                    ->toArray();
            })
            ->disableLabel();
    }

    /**
     * @deprecated Use `->form()` on the action instead.
     */
    protected function getAttachFormSchema(): array
    {
        return $this->getResourceAttachForm()->getSchema();
    }

    /**
     * @deprecated Use `->mountUsing()` on the action instead.
     */
    protected function fillAttachForm(): void
    {
        $this->callHook('beforeFill');
        $this->callHook('beforeAttachFill');

        $this->getMountedTableActionForm()->fill();

        $this->callHook('afterFill');
        $this->callHook('afterAttachFill');
    }

    /**
     * @deprecated Use `->action()` on the action instead.
     */
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
            Notification::make()
                ->title($this->getAttachedNotificationMessage())
                ->success()
                ->send();
        }

        if ($another) {
            $this->getMountedTableAction()->halt();
        }
    }

    /**
     * @deprecated Use `->successNotificationTitle()` on the action instead.
     */
    protected function getAttachedNotificationMessage(): ?string
    {
        return __('filament-support::actions/attach.single.messages.attached');
    }

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function getAttachAction(): Tables\Actions\Action
    {
        return Tables\Actions\AttachAction::make()
            ->form($this->getAttachFormSchema())
            ->mountUsing(fn () => $this->fillAttachForm())
            ->action(fn (array $arguments) => $this->attach($arguments['another'] ?? false));
    }
}
