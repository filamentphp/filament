<?php

namespace App\Livewire\Forms\Fields;

use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Support\Icons\Heroicon;

class ToggleSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('toggle')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Toggle::make('toggle')
                        ->label('Is admin'),
                ]),
            Group::make()
                ->id('toggleIcons')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Toggle::make('toggleIcons')
                        ->label('Is admin')
                        ->onIcon(Heroicon::Bolt)
                        ->offIcon(Heroicon::User),
                ]),
            Group::make()
                ->id('toggleOffColor')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Toggle::make('toggleOffColor')
                        ->label('Is admin')
                        ->default(false)
                        ->onColor('success')
                        ->offColor('danger'),
                ]),
            Group::make()
                ->id('toggleOnColor')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Toggle::make('toggleOnColor')
                        ->label('Is admin')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('danger'),
                ]),
            Group::make()
                ->id('inlineToggle')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Toggle::make('inlineToggle')
                        ->label('Is admin')
                        ->inline(),
                ]),
            Group::make()
                ->id('notInlineToggle')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Toggle::make('notInlineToggle')
                        ->label('Is admin')
                        ->inline(false),
                ]),
        ];
    }
}
