<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class UndoableDeletePostsTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public static bool $canRestore = true;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title'),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->undoable(),
                DeleteAction::make('notUndoable'),
                DeleteAction::make('undoableDisabled')
                    ->undoable(false),
                DeleteAction::make('undoableWithSeconds')
                    ->undoable(seconds: 30),
                DeleteAction::make('undoableWithClosures')
                    ->undoable(fn (): bool => true, fn (): int => 45),
                DeleteAction::make('undoableWithRedirect')
                    ->undoable()
                    ->successRedirectUrl('https://filamentphp.com'),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->undoable(seconds: 20),
                DeleteBulkAction::make('notUndoableBulk'),
                DeleteBulkAction::make('undoableWithoutFetching')
                    ->undoable()
                    ->fetchSelectedRecords(false),
            ]);
    }

    public function getDefaultActionAuthorizationResponse(Action $action): ?Response
    {
        if (($action instanceof RestoreAction) && (! static::$canRestore)) {
            return Response::deny();
        }

        return null;
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
