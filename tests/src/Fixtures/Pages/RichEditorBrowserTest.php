<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\RichEditor;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class RichEditorBrowserTest extends Page
{
    protected string $view = 'pages.rich-editor-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCodeBracket;

    protected static ?int $navigationSort = 18;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                RichEditor::make('content')
                    ->label('Content'),
                RichEditor::make('heightConstrainedContent')
                    ->label('Height constrained content')
                    ->minHeight('12rem')
                    ->maxHeight('14rem')
                    ->extraAttributes(['data-testid' => 'height-constrained-rich-editor']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
