<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ToggleTest extends Page
{
    protected string $view = 'pages.toggle-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBolt;

    protected static ?int $navigationSort = 5;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Toggle::make('toggle')
                    ->label('Basic Toggle')
                    ->extraAttributes(['data-testid' => 'toggle']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
