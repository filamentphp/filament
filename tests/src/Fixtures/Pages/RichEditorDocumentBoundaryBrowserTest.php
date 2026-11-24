<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Forms\Components\RichEditor;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Forms\RichEditor\MinimalControlsCalloutBlock;
use Filament\Tests\Fixtures\Models\Post;
use Livewire\Attributes\Locked;

class RichEditorDocumentBoundaryBrowserTest extends Page
{
    protected string $view = 'pages.rich-editor-document-boundary-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    #[Locked]
    public int $recordId;

    public int $saveCount = 0;

    public function mount(): void
    {
        $this->recordId = request()->integer('record');
        $record = Post::findOrFail($this->recordId);

        $this->form->fill([
            'jsonContent' => $record->json['content'],
            'htmlContent' => $record->content,
            'disabledContent' => $record->json['content'],
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                RichEditor::make('jsonContent')
                    ->label('JSON content')
                    ->json()
                    ->customBlocks([MinimalControlsCalloutBlock::class])
                    ->extraAttributes(['data-testid' => 'json-boundary-editor']),
                RichEditor::make('htmlContent')
                    ->label('HTML content')
                    ->customBlocks([MinimalControlsCalloutBlock::class])
                    ->extraAttributes(['data-testid' => 'html-boundary-editor']),
                RichEditor::make('disabledContent')
                    ->label('Disabled content')
                    ->json()
                    ->disabled()
                    ->customBlocks([MinimalControlsCalloutBlock::class])
                    ->extraAttributes(['data-testid' => 'disabled-boundary-editor']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        Post::findOrFail($this->recordId)->update([
            'json' => ['content' => $state['jsonContent']],
            'content' => $state['htmlContent'],
        ]);

        $this->saveCount++;
    }
}
