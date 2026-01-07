<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class RepeaterTest extends Page
{
    protected string $view = 'pages.repeater-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static ?int $navigationSort = 6;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Repeater::make('items')
                    ->label('Items')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required(),
                    ])
                    ->extraAttributes(['data-testid' => 'repeater']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
