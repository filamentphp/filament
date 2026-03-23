<?php

namespace App\Livewire\Forms\Fields;

use Filament\Forms\Components\MarkdownEditor;
use Filament\Schemas\Components\Group;

class MarkdownEditorSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('markdownEditor')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    MarkdownEditor::make('markdownEditor')
                        ->label('Content'),
                ]),
            Group::make()
                ->id('markdownEditorCustomToolbar')
                ->extraAttributes([
                    'class' => 'p-16 max-w-5xl',
                ])
                ->schema([
                    MarkdownEditor::make('markdownEditorCustomToolbar')
                        ->label('Content')
                        ->toolbarButtons([
                            ['bold', 'italic', 'strike', 'link'],
                            ['heading'],
                        ]),
                ]),
        ];
    }
}
