<?php

namespace App\Livewire\Schemas\Layout;

use Filament\Actions\Action;
use Filament\Schemas\Components\EmptyState;
use Filament\Schemas\Components\Group;
use Filament\Support\Icons\Heroicon;

class EmptyStateSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('emptyState')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    EmptyState::make('No users yet')
                        ->description('Get started by creating a new user.')
                        ->icon(Heroicon::OutlinedUser)
                        ->footer([
                            Action::make('createUser')
                                ->icon(Heroicon::Plus),
                        ]),
                ]),
            Group::make()
                ->id('emptyStateContainedFalse')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    EmptyState::make('No users yet')
                        ->description('Get started by creating a new user.')
                        ->icon(Heroicon::OutlinedUser)
                        ->footer([
                            Action::make('createUser')
                                ->label('Create user')
                                ->icon(Heroicon::Plus),
                        ])
                        ->contained(false),
                ]),
        ];
    }
}
