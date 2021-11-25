<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait UsesResourceTable
{
    protected ?Table $resourceTable = null;

    protected function getResourceTable(): Table
    {
        if (! $this->resourceTable) {
            $table = Table::make();

            $resource = static::getResource();

            if ($resource::hasPage('view')) {
                $table->actions([
                    Tables\Actions\LinkAction::make('view')
                        ->label('View')
                        ->url(fn (Model $record): string => $resource::getUrl('view', ['record' => $record]))
                        ->hidden(fn (Model $record): bool => ! $resource::canView($record)),
                ]);
            } elseif ($resource::hasPage('edit')) {
                $table->actions([
                    Tables\Actions\LinkAction::make('edit')
                        ->label('Edit')
                        ->url(fn (Model $record): string => $resource::getUrl('edit', ['record' => $record]))
                        ->hidden(fn (Model $record): bool => ! $resource::canEdit($record)),
                ]);
            }

            if ($resource::canDeleteAny()) {
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

            $this->resourceTable = static::getResource()::table($table);
        }

        return $this->resourceTable;
    }
}
