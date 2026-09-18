<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class BuilderSearchableTest extends Page
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
                    ->searchable()
                    ->addAction(fn (Action $action): Action => $action->extraAttributes(['data-testid' => 'add-block']))
                    ->blocks([
                        Builder\Block::make('paragraph')
                            ->label('Paragraph')
                            ->schema([
                                TextInput::make('text')
                                    ->label('Text'),
                            ]),
                        Builder\Block::make('heading')
                            ->label(new HtmlString('Research &amp; Development'))
                            ->schema([
                                TextInput::make('title')
                                    ->label('Title'),
                            ]),
                        Builder\Block::make('video')
                            ->label('Video')
                            ->maxItems(1)
                            ->schema([
                                TextInput::make('url')
                                    ->label('URL'),
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
