<?php

namespace App\Livewire\Forms\Fields;

use Filament\Forms\Components\Radio;
use Filament\Schemas\Components\Group;

class RadioSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('radio')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Radio::make('radio')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                        ])
                        ->default('draft'),
                ]),
            Group::make()
                ->id('radioOptionDescriptions')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Radio::make('radioOptionDescriptions')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                        ])
                        ->descriptions([
                            'draft' => 'Is not visible.',
                            'scheduled' => 'Will be visible.',
                            'published' => 'Is visible.',
                        ])
                        ->default('draft'),
                ]),
            Group::make()
                ->id('booleanRadio')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Radio::make('booleanRadio')
                        ->label('Like this post?')
                        ->boolean()
                        ->default(true),
                ]),
            Group::make()
                ->id('booleanRadioCustomLabels')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Radio::make('booleanRadioCustomLabels')
                        ->label('Receive notifications?')
                        ->boolean('Enable', 'Disable')
                        ->default(true),
                ]),
            Group::make()
                ->id('inlineRadio')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Radio::make('inlineRadio')
                        ->label('Like this post?')
                        ->boolean()
                        ->inline()
                        ->default(true),
                ]),
            Group::make()
                ->id('disabledOptionRadio')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    Radio::make('disabledOptionRadio')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'scheduled' => 'Scheduled',
                            'published' => 'Published',
                        ])
                        ->default('draft')
                        ->disableOptionWhen(fn (string $value): bool => $value === 'published'),
                ]),
        ];
    }
}
