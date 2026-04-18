<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\Slider;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SliderBrowserTest extends Page
{
    protected string $view = 'pages.slider-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?int $navigationSort = 33;

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Slider::make('value')
                    ->label('Test Slider'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
