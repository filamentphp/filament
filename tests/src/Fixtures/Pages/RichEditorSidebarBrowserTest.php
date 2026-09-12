<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Forms\RichEditor\SidebarImageBlock;
use Filament\Tests\Fixtures\Forms\RichEditor\SidebarQuoteBlock;
use Filament\Tests\Fixtures\Forms\RichEditor\SidebarSectionBlock;

class RichEditorSidebarBrowserTest extends Page
{
    protected string $view = 'pages.rich-editor-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public bool $hasGrid = false;

    public function mount(): void
    {
        $this->hasGrid = request()->boolean('grid');

        $this->form->fill([
            'content' => '<p>First paragraph.</p><p>Last paragraph.</p>',
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                $this->makeEditor('content')
                    ->customBlocksGrid($this->hasGrid)
                    ->extraAttributes(['data-testid' => 'sidebar-rich-editor']),
            ])
            ->statePath('data');
    }

    protected function makeEditor(string $name): RichEditor
    {
        return RichEditor::make($name)
            ->toolbarButtons([['bold', 'italic', 'customBlocks']])
            ->customBlocks([
                'Editorial' => [SidebarQuoteBlock::class, SidebarSectionBlock::class],
                'Media' => [SidebarImageBlock::class],
            ])
            ->activePanel('customBlocks')
            ->searchableCustomBlocks()
            ->stickyToolbar()
            ->stickyPanels();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('editInModal')
                ->label('Edit in modal')
                ->schema([
                    $this->makeEditor('modalContent')
                        ->stickyOffset('0px')
                        ->default(str_repeat('<p>A paragraph in a long document.</p>', 60))
                        ->extraAttributes(['data-testid' => 'modal-rich-editor']),
                ])
                ->modalSubmitAction(false),
        ];
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
