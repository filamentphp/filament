<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Actions\Action;
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

    public bool $isReorderingTest = false;

    public bool $isFullRender = false;

    public bool $hasVisibleMoveButtons = false;

    public bool $isAncestorPartial = false;

    public function mount(): void
    {
        $this->isReorderingTest = request()->boolean('reordering');
        $this->isFullRender = request()->boolean('full');
        $this->hasVisibleMoveButtons = $this->isReorderingTest && (! request()->boolean('screenReader'));
        $this->isAncestorPartial = request()->boolean('ancestor');
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Builder::make('content')
                    ->label('Content')
                    ->reorderableWithButtons($this->hasVisibleMoveButtons)
                    ->generateUuidUsing(true)
                    ->default($this->isReorderingTest ? [
                        ['type' => 'paragraph', 'data' => ['text' => 'Alpha']],
                        ['type' => 'secret', 'data' => ['text' => 'Hidden']],
                        ['type' => 'paragraph', 'data' => ['text' => 'Beta']],
                        ['type' => 'paragraph', 'data' => ['text' => 'Gamma']],
                    ] : [])
                    ->collapsed($this->isReorderingTest)
                    ->partiallyRenderAfterActionsCalled((! $this->isFullRender) && (! $this->isAncestorPartial))
                    ->addAction(static fn (Action $action): Action => $action->extraAttributes(['data-testid' => 'add-block']))
                    ->addBetweenAction(static fn (Action $action): Action => $action->extraAttributes(['data-testid' => 'add-between']))
                    ->deleteAction(static fn (Action $action): Action => $action->extraAttributes(['data-testid' => 'delete-block']))
                    ->blocks([
                        Builder\Block::make('secret')->hidden()->schema([TextInput::make('text')]),
                        Builder\Block::make('paragraph')
                            ->label(static fn (?array $state): string => $state['text'] ?? 'Paragraph')
                            ->schema([
                                TextInput::make('text')
                                    ->label('Text')
                                    ->default(fn (): string => 'Paragraph ' . count($this->data['content'] ?? []))
                                    ->extraInputAttributes(['data-testid' => 'paragraph-text'])
                                    ->required(),
                            ]),
                        Builder\Block::make('heading')
                            ->label('Heading')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Title')
                                    ->extraInputAttributes(['data-testid' => 'heading-title'])
                                    ->required(),
                            ]),
                    ])
                    ->extraAttributes(['data-testid' => 'builder']),
            ])
            ->partiallyRender($this->isAncestorPartial)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
