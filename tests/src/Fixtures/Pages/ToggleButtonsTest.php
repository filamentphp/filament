<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\ToggleButtons;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
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
                    ->size(Size::Small)
                    ->extraAttributes(['data-testid' => 'toggle-buttons']),
                ToggleButtons::make('grouped_field')
                    ->label('Grouped ToggleButtons')
                    ->options(['a' => 'Option A', 'b' => 'Option B'])
                    ->icons([
                        'a' => Heroicon::Check,
                        'b' => Heroicon::XMark,
                    ])
                    ->size(Size::ExtraSmall)
                    ->grouped(),
                ToggleButtons::make('fullWidthStacked')
                    ->label('Full-width stacked ToggleButtons')
                    ->options(['a' => 'Option A', 'b' => 'Option B'])
                    ->fullWidth(),
                ToggleButtons::make('fullWidthInline')
                    ->label('Full-width inline ToggleButtons')
                    ->options([
                        'draft' => 'Draft',
                        'review' => 'Needs additional review',
                        'response' => 'Awaiting customer response',
                        'published' => 'Published publicly',
                    ])
                    ->inline()
                    ->fullWidth(),
                ToggleButtons::make('fullWidthGrouped')
                    ->label('Full-width grouped ToggleButtons')
                    ->options(['a' => 'Option A', 'b' => 'Option B'])
                    ->grouped()
                    ->fullWidth(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
