<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Forms\Components\RichEditor;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Forms\RichEditor\MinimalControlsCalloutBlock;
use Filament\Tests\Fixtures\Forms\RichEditor\MinimalControlsDividerBlock;

class RichEditorMinimalControlsBrowserTest extends Page
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
                $this->makeEditor('minimalContent')
                    ->label('Minimal controls')
                    ->minimalCustomBlockControls()
                    ->extraAttributes(['data-testid' => 'minimal-controls-editor']),
                $this->makeEditor('defaultContent')
                    ->label('Default controls')
                    ->extraAttributes(['data-testid' => 'default-controls-editor']),
                $this->makeEditor('disabledContent')
                    ->label('Disabled editor')
                    ->minimalCustomBlockControls()
                    ->disabled()
                    ->extraAttributes(['data-testid' => 'disabled-controls-editor']),
            ])
            ->statePath('data');
    }

    protected function makeEditor(string $name): RichEditor
    {
        return RichEditor::make($name)
            ->json()
            ->toolbarButtons([['undo', 'redo']])
            ->customBlocks([MinimalControlsCalloutBlock::class, MinimalControlsDividerBlock::class])
            ->default(static::getDocument());
    }

    /**
     * @return array<string, mixed>
     */
    public static function getDocument(): array
    {
        return [
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Opening paragraph.']]],
                ['type' => 'customBlock', 'attrs' => ['id' => 'callout', 'config' => ['message' => 'First callout.']]],
                ['type' => 'customBlock', 'attrs' => ['id' => 'callout', 'config' => ['message' => 'Second callout.']]],
                ['type' => 'customBlock', 'attrs' => ['id' => 'divider', 'config' => []]],
                ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Closing paragraph.']]],
            ],
        ];
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
