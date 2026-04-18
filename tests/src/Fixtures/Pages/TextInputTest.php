<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TextInputTest extends Page
{
    protected string $view = 'pages.text-input-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedPencilSquare;

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
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->extraAttributes(['data-testid' => 'text-input']),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->extraAttributes(['data-testid' => 'email-input']),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->extraAttributes(['data-testid' => 'password-input']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
