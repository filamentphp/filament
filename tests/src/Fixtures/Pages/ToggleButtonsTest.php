<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\ToggleButtons;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ToggleButtonsTest extends Page
{
    protected string $view = 'pages.toggle-buttons-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?int $navigationSort = 13;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ToggleButtons::make('field')
                    ->label('Test ToggleButtons')
                    ->options(['a' => 'Option A', 'b' => 'Option B'])
                    ->extraAttributes(['data-testid' => 'toggle-buttons']),
                ToggleButtons::make('fullWidthStacked')
                    ->label('Full-width stacked ToggleButtons')
                    ->options(['a' => 'Option A', 'b' => 'Option B'])
                    ->fullWidth()
                    ->extraAttributes(['data-testid' => 'full-width-stacked-toggle-buttons'])
                    ->extraFieldWrapperAttributes(['style' => 'width: 18rem']),
                ToggleButtons::make('fullWidthInline')
                    ->label('Full-width inline ToggleButtons')
                    ->options([
                        'draft' => 'Draft',
                        'review' => 'Needs additional review',
                        'response' => 'Awaiting customer response',
                        'published' => 'Published publicly',
                    ])
                    ->inline()
                    ->fullWidth()
                    ->extraAttributes(['data-testid' => 'full-width-inline-toggle-buttons'])
                    ->extraFieldWrapperAttributes(['style' => 'width: 18rem']),
                ToggleButtons::make('fullWidthGrouped')
                    ->label('Full-width grouped ToggleButtons')
                    ->options(['a' => 'Option A', 'b' => 'Option B'])
                    ->grouped()
                    ->fullWidth()
                    ->extraAttributes(['data-testid' => 'full-width-grouped-toggle-buttons'])
                    ->extraFieldWrapperAttributes(['style' => 'width: 18rem']),
                ToggleButtons::make('groupedWithLongLabels')
                    ->label('Grouped ToggleButtons with long labels')
                    ->options([
                        'review' => 'Needs additional editorial review before publishing',
                        'response' => 'Awaiting a detailed response from the customer',
                    ])
                    ->grouped()
                    ->extraAttributes(['data-testid' => 'grouped-toggle-buttons-with-long-labels'])
                    ->extraFieldWrapperAttributes(['style' => 'width: 18rem']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
