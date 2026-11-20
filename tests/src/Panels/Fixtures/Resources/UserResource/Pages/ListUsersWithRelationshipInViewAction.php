<?php

namespace Filament\Tests\Panels\Fixtures\Resources\UserResource\Pages;

use Filament\Forms\Components\Select;
use Filament\Tables\Actions\ViewAction;

class ListUsersWithRelationshipInViewAction extends ListUsers
{
    protected function configureViewAction(ViewAction $action): void
    {
        parent::configureViewAction($action);

        $action
            ->url(null)
            ->infolist([])
            ->form([
                Select::make('teams')
                    ->relationship('teams', 'name')
                    ->multiple()
                    ->saveRelationshipsWhenDisabled(),
            ]);
    }
}
