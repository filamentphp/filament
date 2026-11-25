<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Models\UserWithStringKeyPosts;
use Illuminate\Support\Str;

class RelationshipKeyBrowserTest extends Page
{
    protected string $view = 'pages.relationship-key-browser-test';

    protected static bool $shouldRegisterNavigation = false;

    public array $data = [];

    public bool $isSaved = false;

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model(UserWithStringKeyPosts::findOrFail(auth()->id()))
            ->statePath('data')
            ->components([
                TextEntry::make('control')->state(static fn (): string => (string) Str::uuid())
                    ->extraAttributes(['data-testid' => 'poll-control']),
                Repeater::make('posts')->relationship('posts')->schema([
                    TextEntry::make('stamp')->state(static fn (): string => (string) Str::uuid())
                        ->poll('1s')->extraAttributes(['data-testid' => 'poll-stamp']),
                    Toggle::make('is_published')->extraAttributes(['data-testid' => 'published-toggle']),
                    Builder::make('config')->blocks([
                        Block::make('paragraph')->schema([
                            TextInput::make('text')->extraInputAttributes(['data-testid' => 'block-text']),
                        ]),
                    ])->extraAttributes(['data-testid' => 'post-builder']),
                    MarkdownEditor::make('content')->extraAttributes(['data-testid' => 'post-markdown']),
                ])->reorderable(false)->addable(false)->deletable(false),
            ]);
    }

    public function save(): void
    {
        $this->form->getState();
        $this->isSaved = true;
    }
}
