<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class BuilderTest extends Page
{
    protected string $view = 'pages.builder-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?int $navigationSort = 7;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Builder::make('content')
                    ->label('Content')
                    ->blocks([
                        Builder\Block::make('paragraph')
                            ->label('Paragraph')
                            ->schema([
                                TextInput::make('text')
                                    ->label('Text')
                                    ->required(),
                            ]),
                        Builder\Block::make('heading')
                            ->label('Heading')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Title')
                                    ->required(),
                            ]),
                    ])
                    ->extraAttributes(['data-testid' => 'builder']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
