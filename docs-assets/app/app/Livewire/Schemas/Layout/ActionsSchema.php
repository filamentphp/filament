<?php

namespace App\Livewire\Schemas\Layout;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\Icons\Heroicon;

class ActionsSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('independentActions')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Actions::make([
                        Action::make('star')
                            ->icon(Heroicon::Star),
                        Action::make('resetStars')
                            ->icon(Heroicon::XMark)
                            ->color('danger'),
                    ]),
                ]),
            Group::make()
                ->id('independentActionsFullWidth')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Actions::make([
                        Action::make('star')
                            ->icon(Heroicon::Star),
                        Action::make('resetStars')
                            ->icon(Heroicon::XMark)
                            ->color('danger'),
                    ])->fullWidth(),
                ]),
            Group::make()
                ->id('independentActionsHorizontallyAlignedCenter')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Actions::make([
                        Action::make('star')
                            ->icon(Heroicon::Star),
                        Action::make('resetStars')
                            ->icon(Heroicon::XMark)
                            ->color('danger'),
                    ])->alignment(Alignment::Center),
                ]),
            Group::make()
                ->id('independentActionsVerticallyAlignedEnd')
                ->extraAttributes([
                    'class' => 'p-16 max-w-2xl',
                ])
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('stars')
                                ->default('4572100479'),
                            Actions::make([
                                Action::make('star')
                                    ->icon(Heroicon::Star),
                                Action::make('resetStars')
                                    ->icon(Heroicon::XMark)
                                    ->color('danger'),
                            ])->verticalAlignment(VerticalAlignment::End),
                        ]),
                ]),
        ];
    }
}
