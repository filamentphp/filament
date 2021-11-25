<?php

namespace Filament\Resources\RelationManagers;

use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HasManyRelationManager extends RelationManager
{
    protected ?Form $resourceForm = null;

    protected ?Table $resourceTable = null;

    protected static string $title;

    protected static string $view = 'filament::resources.relation-managers.has-many-relation-manager';

    protected function getResourceForm(): Form
    {
        if (! $this->resourceForm) {
            $this->resourceForm = static::form(Form::make($this));
        }

        return $this->resourceForm;
    }

    protected function getResourceTable(): Table
    {
        if (! $this->resourceTable) {
            $table = Table::make();

            $table->actions([
                Tables\Actions\LinkAction::make('edit')
                    ->label('Edit')
                    ->form($this->getEditFormSchema())
                    ->mountUsing(fn () => $this->fillEditForm())
                    ->action(fn () => $this->saveRecord())
                    ->modalWidth('4xl')
                    ->hidden(fn (Model $record): bool => ! static::canEdit($record)),
            ]);

            if ($this->canDeleteAny()) {
                $table->bulkActions([
                    Tables\Actions\BulkAction::make('delete')
                        ->label('Delete selected')
                        ->action(fn (Collection $records) => $records->each->delete())
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->color('danger')
                        ->icon('heroicon-o-trash'),
                ]);
            }

            if ($this->canCreate()) {
                $table->headerActions([
                    Tables\Actions\ButtonAction::make('create')
                        ->label('Create')
                        ->form($this->getCreateFormSchema())
                        ->mountUsing(fn () => $this->fillCreateForm())
                        ->action(fn () => $this->createRecord())
                        ->modalWidth('4xl'),
                ]);
            }

            $this->resourceTable = static::table($table);
        }

        return $this->resourceTable;
    }

    protected function getCreateFormSchema(): array
    {
        return $this->getResourceForm()->getSchema();
    }

    protected function fillCreateForm(): void
    {
        $this->callHook('beforeFill');
        $this->callHook('beforeCreateFill');

        $this->getMountedTableActionForm()->fill();

        $this->callHook('afterFill');
        $this->callHook('afterCreateFill');
    }

    protected function fillEditForm(): void
    {
        $this->callHook('beforeFill');
        $this->callHook('beforeEditFill');

        $this->getMountedTableActionForm()->fill(
            $this->getMountedTableActionRecord()->toArray(),
        );

        $this->callHook('afterFill');
        $this->callHook('afterEditFill');
    }

    protected function createRecord(): void
    {
        $this->callHook('beforeValidate');
        $this->callHook('beforeCreateValidate');

        $data = $this->getMountedTableActionForm()->getState();

        $this->callHook('afterValidate');
        $this->callHook('afterCreateValidate');

        $this->callHook('beforeCreate');

        $record = $this->getRelationship()->create($data);
        $this->getMountedTableActionForm()->model($record)->saveRelationships();

        $this->callHook('afterCreate');
    }

    protected function saveRecord(): void
    {
        $this->callHook('beforeValidate');
        $this->callHook('beforeEditValidate');

        $data = $this->getMountedTableActionForm()->getState();

        $this->callHook('afterValidate');
        $this->callHook('afterEditValidate');

        $this->callHook('beforeSave');

        $this->getMountedTableActionRecord()->update($data);

        $this->callHook('afterSave');
    }

    protected function getEditFormSchema(): array
    {
        return $this->getResourceForm()->getSchema();
    }

    protected function getTableActions(): array
    {
        return $this->getResourceTable()->getActions();
    }

    protected function getTableBulkActions(): array
    {
        return $this->getResourceTable()->getBulkActions();
    }

    protected function getTableColumns(): array
    {
        return $this->getResourceTable()->getColumns();
    }

    protected function getTableFilters(): array
    {
        return $this->getResourceTable()->getFilters();
    }

    protected function getTableHeaderActions(): array
    {
        return $this->getResourceTable()->getHeaderActions();
    }

    protected function getTableHeading(): ?string
    {
        return static::getTitle();
    }

    protected function getTableQuery(): Builder
    {
        return $this->getRelationship()->getQuery();
    }
}
