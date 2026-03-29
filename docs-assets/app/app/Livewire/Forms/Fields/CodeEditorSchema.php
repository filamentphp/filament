<?php

namespace App\Livewire\Forms\Fields;

use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Schemas\Components\Group;

class CodeEditorSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('codeEditor')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    CodeEditor::make('code')
                        ->default(<<<'YAML'
                            name: Filament
                            framework: Laravel
                            packageManager: Composer
                            releaseYear: 2021
                            website: https://filamentphp.com
                            YAML),
                ]),
            Group::make()
                ->id('codeEditorLanguage')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    CodeEditor::make('codeWithLanguage')
                        ->label('Code')
                        ->language(Language::JavaScript)
                        ->default(<<<'JS'
                            const fetchUser = async (id) => {
                                const res = await fetch(`https://api.example.com/users/${id}`)

                                if (! res.ok) {
                                    throw new Error('User not found')
                                }

                                return res.json()
                            }

                            fetchUser(1)
                                .then((user) => console.log(`👤 ${user.name}`))
                                .catch((error) => console.error('⚠️', error.message))
                            JS),
                ]),
            Group::make()
                ->id('codeEditorWrap')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    CodeEditor::make('codeWrap')
                        ->label('Code')
                        ->wrap()
                        ->language(Language::Php)
                        ->default(<<<'PHP'
                            public function getUserDisplayName(User $user): string { return $user->first_name . ' ' . $user->last_name . ' (' . $user->email . ')'; }

                            public function getFormattedAddress(Address $address): string { return $address->street . ', ' . $address->city . ', ' . $address->state . ' ' . $address->zip; }
                            PHP),
                ]),
        ];
    }
}
