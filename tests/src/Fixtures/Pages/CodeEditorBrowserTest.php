<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\CodeEditor;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class CodeEditorBrowserTest extends Page
{
    protected string $view = 'pages.code-editor-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCodeBracket;

    protected static ?int $navigationSort = 30;

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public ?string $language = null;

    public bool $isDisabled = false;

    public ?string $savedCode = null;

    public function mount(): void
    {
        $this->language = request()->boolean('customLanguage') ? '/code-editor-language.js' : null;
        $this->isDisabled = request()->boolean('disabled');
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                CodeEditor::make('code')
                    ->label('Test Code Editor')
                    ->language($this->language)
                    ->disabled($this->isDisabled),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->savedCode = $this->form->getState()['code'] ?? null;
    }
}
