<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Forms\Components\RichEditor;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class RichEditorStyleAttributesBrowserTest extends Page
{
    protected string $view = 'pages.rich-editor-browser-test';

    protected static bool $shouldRegisterNavigation = false;

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
                    ->label('Content')
                    ->json()
                    ->disabled()
                    ->extraAttributes(['data-testid' => 'style-attributes-rich-editor']),
            ])
            ->statePath('data');
    }
}
