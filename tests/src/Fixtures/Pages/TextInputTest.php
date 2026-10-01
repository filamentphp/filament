<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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

                TextInput::make('code')
                    ->label('Code')
                    ->copyable()
                    ->extraAttributes(['data-testid' => 'copyable-input']),

                Toggle::make('useNumericDefaults')
                    ->label('Use numeric defaults')
                    ->live()
                    ->extraAttributes(['data-testid' => 'numeric-defaults-toggle']),

                TextInput::make('amount')
                    ->numeric(fn (): bool => (bool) ($this->data['useNumericDefaults'] ?? false))
                    ->extraInputAttributes(['data-testid' => 'dynamic-numeric-input']),

                TextInput::make('quantity')
                    ->integer(fn (): bool => (bool) ($this->data['useNumericDefaults'] ?? false))
                    ->extraInputAttributes(['data-testid' => 'dynamic-integer-input']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
