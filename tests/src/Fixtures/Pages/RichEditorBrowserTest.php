<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Forms\RichEditor\SidebarImageBlock;
use Filament\Tests\Fixtures\Forms\RichEditor\SidebarQuoteBlock;
use Filament\Tests\Fixtures\Forms\RichEditor\SidebarSectionBlock;

class RichEditorBrowserTest extends Page
{
    protected string $view = 'pages.rich-editor-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCodeBracket;

    protected static ?int $navigationSort = 18;

    public ?array $data = [];

    public int $editorVersion = 0;

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
                    ->default(<<<'HTML'
                        <p>Before grid.</p>
                        <div class="grid-layout" data-cols="2" data-from-breakpoint="md">
                            <div class="grid-layout-col" data-col-span="1">
                                <p>First column.</p>
                                <h2>Column heading</h2>
                            </div>
                            <div class="grid-layout-col" data-col-span="1">
                                <p>Second column.</p>
                                <ul><li><p>List item.</p></li></ul>
                            </div>
                        </div>
                        <p>After grid.</p>
                        HTML)
                    ->extraAttributes(['data-testid' => 'default-rich-editor']),
                RichEditor::make('heightConstrainedContent')
                    ->label('Height constrained content')
                    ->minHeight('12rem')
                    ->maxHeight('14rem')
                    ->extraAttributes(['data-testid' => 'height-constrained-rich-editor']),
                RichEditor::make('customBlocksContent')
                    ->label('Custom blocks content')
                    ->toolbarButtons([['bold', 'italic', 'customBlocks']])
                    ->customBlocks([
                        'Editorial' => [SidebarQuoteBlock::class, SidebarSectionBlock::class],
                        'Media' => [SidebarImageBlock::class],
                    ])
                    ->activePanel('customBlocks')
                    ->customBlocksGrid()
                    ->searchableCustomBlocks()
                    ->stickyToolbar()
                    ->stickyPanels()
                    ->default('<p>First paragraph.</p><p>Last paragraph.</p>')
                    ->extraAttributes(['data-testid' => 'custom-blocks-rich-editor']),
                RichEditor::make('keyboardContent')
                    ->label('Article')
                    ->default('<p>Alpha beta.</p>')
                    ->toolbarButtons([
                        ['bold', 'unavailable', 'italic'],
                        [ToolbarButtonGroup::make('Formatting "advanced"', ['bold', 'unavailable', 'italic', 'link'])->textualButtons()],
                        ['customUnderline"advanced', 'link'],
                    ])
                    ->tools([
                        RichEditorTool::make('unavailable')
                            ->label('Unavailable command')
                            ->icon(Heroicon::NoSymbol)
                            ->activeJsExpression('false')
                            ->disabledWhenNotActive()
                            ->jsHandler('$getEditor().chain().focus().insertContent(\'Unexpected text\').run()'),
                        RichEditorTool::make('customUnderline"advanced')
                            ->label('Custom underline')
                            ->icon(Heroicon::Underline)
                            ->activeKey('underline')
                            ->toggle()
                            ->jsHandler('$getEditor().chain().focus().toggleUnderline().run()'),
                    ])
                    ->floatingToolbars([
                        'paragraph' => ['bold', ToolbarButtonGroup::make('Selection actions', ['italic', 'link'])->textualButtons(), 'link'],
                    ])
                    ->extraAttributes(fn (): array => [
                        'data-testid' => 'keyboard-rich-editor',
                        'wire:key' => "keyboard-editor-{$this->editorVersion}",
                    ]),
                RichEditor::make('duplicateGroupsContent')
                    ->label('Duplicate toolbar groups')
                    ->default('<p>Alpha beta.</p>')
                    ->toolbarButtons([
                        [ToolbarButtonGroup::make('Formatting', ['bold', 'unavailable', 'italic', 'link'])],
                        [ToolbarButtonGroup::make('Formatting', ['italic', 'link'])->textualButtons()],
                    ])
                    ->tools([
                        RichEditorTool::make('unavailable')
                            ->label('Unavailable command')
                            ->icon(Heroicon::NoSymbol)
                            ->activeJsExpression('false')
                            ->disabledWhenNotActive()
                            ->jsHandler('$getEditor().chain().focus().insertContent(\'Unexpected text\').run()'),
                    ])
                    ->extraAttributes(fn (): array => [
                        'data-testid' => 'duplicate-groups-rich-editor',
                        'wire:key' => "duplicate-groups-editor-{$this->editorVersion}",
                    ]),
                RichEditor::make('nestedContent')
                    ->label('Nested layout')
                    ->default(<<<'HTML'
                        <div class="grid-layout" data-cols="2" data-from-breakpoint="md">
                            <div class="grid-layout-col" data-col-span="1">
                                <table><tbody><tr><td><p>Nested cell.</p></td><td><p>Other cell.</p></td></tr></tbody></table>
                            </div>
                            <div class="grid-layout-col" data-col-span="1"><p>Other column.</p></div>
                        </div>
                        HTML)
                    ->extraAttributes(['data-testid' => 'nested-rich-editor']),
                RichEditor::make('revealedContent')
                    ->label('Revealed article')
                    ->toolbarButtons([['italic', 'bold']])
                    ->extraAttributes([
                        'data-testid' => 'revealed-rich-editor',
                        'x-data' => '{ isVisible: false }',
                        'x-show' => 'isVisible',
                    ]),
            ])
            ->statePath('data');
    }

    public function remountEditor(): void
    {
        $this->editorVersion++;
        $this->forceRender();
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
