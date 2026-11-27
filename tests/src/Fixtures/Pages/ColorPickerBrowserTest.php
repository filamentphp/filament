<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\ColorPicker;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ColorPickerBrowserTest extends Page
{
    protected string $view = 'pages.color-picker-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static ?int $navigationSort = 11;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ColorPicker::make('field')
                    ->label('Test Color')
                    ->live(debounce: 1000)
                    ->extraAttributes(['data-testid' => 'color-picker']),
                ColorPicker::make('brandColor')
                    ->label('Brand color')
                    ->presetColors([
                        '#ef4444' => 'Red',
                        '#3b82f6' => 'Blue',
                        '#111827',
                    ])
                    ->eyeDropper()
                    ->extraInputAttributes(['data-testid' => 'brand-color-picker']),
                ColorPicker::make('accentColor')
                    ->label('Accent color')
                    ->presetColors(['#22c55e' => 'Green'])
                    ->extraInputAttributes(['data-testid' => 'accent-color-picker']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
