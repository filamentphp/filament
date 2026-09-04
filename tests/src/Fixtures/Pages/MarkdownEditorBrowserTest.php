<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class MarkdownEditorBrowserTest extends Page
{
    protected string $view = 'pages.markdown-editor-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedHashtag;

    protected static ?int $navigationSort = 19;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                MarkdownEditor::make('content')
                    ->label('Content'),
                MarkdownEditor::make('nullMinHeightContent')
                    ->label('Null minimum height content')
                    ->minHeight('20rem')
                    ->minHeight(null)
                    ->extraAttributes(['data-testid' => 'null-min-height-markdown-editor']),
                MarkdownEditor::make('nullMinHeightWithMaxHeightContent')
                    ->label('Null minimum height with maximum height content')
                    ->minHeight(null)
                    ->maxHeight('12rem')
                    ->extraAttributes(['data-testid' => 'null-min-height-with-max-height-markdown-editor']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
