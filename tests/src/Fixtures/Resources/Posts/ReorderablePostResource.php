<?php

namespace Filament\Tests\Fixtures\Resources\Posts;

use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Auth\Access\Response;

class ReorderablePostResource extends PostResource
{
    public static function getReorderAuthorizationResponse(): Response
    {
        return Response::allow();
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')])
            ->recordTitleAttribute('title')
            ->reorderable('sort')
            ->defaultSort('sort')
            ->paginated(false)
            ->reorderRecordsTriggerAction(static fn (Action $action): Action => $action->extraAttributes(['data-testid' => 'toggle-reordering']));
    }
}
