<?php

namespace App\Livewire\Forms\Fields;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Support\Icons\Heroicon;

class TextInputSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('textInput')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('textInput')
                        ->label('Name')
                        ->default('Dan Harrin'),
                ]),
            Group::make()
                ->id('textInputAffix')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('textInputAffix')
                        ->label('Domain')
                        ->default('filamentphp')
                        ->prefix('https://')
                        ->suffix('.com'),
                ]),
            Group::make()
                ->id('textInputSuffixIcon')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('textInputSuffixIcon')
                        ->label('Domain')
                        ->default('https://filamentphp.com')
                        ->suffixIcon(Heroicon::GlobeAlt),
                ]),
            Group::make()
                ->id('textInputSuffixIconColor')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('textInputSuffixIconColor')
                        ->label('Domain')
                        ->default('https://filamentphp.com')
                        ->suffixIcon(Heroicon::CheckCircle)
                        ->suffixIconColor('success'),
                ]),
            Group::make()
                ->id('textInputRevealablePassword')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('textInputRevealablePassword')
                        ->label('Password')
                        ->default('filament123')
                        ->password()
                        ->revealable(),
                    TextInput::make('textInputRevealedPassword')
                        ->label('Password')
                        ->default('filament123')
                        ->suffixActions([
                            TextInput\Actions\HidePasswordAction::make()
                                ->extraAttributes([]),
                        ]),
                ]),
            Group::make()
                ->id('textInputMask')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('textInputMask')
                        ->label('Phone number')
                        ->mask('(999) 999-9999')
                        ->placeholder('(555) 555-5555')
                        ->tel(),
                ]),
            Group::make()
                ->id('textInputCopyable')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('textInputCopyable')
                        ->label('API key')
                        ->default('flm_sk_1a2b3c4d5e6f7g8h9i0j')
                        ->copyable(copyMessage: 'Copied!'),
                ]),
            Group::make()
                ->id('textInputColor')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    TextInput::make('textInputColor')
                        ->label('Background color')
                        ->type('color')
                        ->default('#6366f1'),
                ]),
        ];
    }
}
