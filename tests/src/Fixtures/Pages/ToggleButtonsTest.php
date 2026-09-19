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
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
