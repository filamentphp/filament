<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class BuilderLazyBrowserTest extends Page
{
    protected string $view = 'pages.builder-lazy-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?int $navigationSort = 8;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'content' => [
                ['type' => 'paragraph', 'data' => ['text' => 'Hello world']],
            ],
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Builder::make('content')
                    ->label('Content')
                    ->lazy()
                    ->blocks([
                        Builder\Block::make('paragraph')
                            ->label('Paragraph')
                            ->schema([
                                TextInput::make('text')
                                    ->label('Text')
                                    ->required(),
                            ]),
                    ])
                    ->extraAttributes(['data-testid' => 'lazy-builder']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
