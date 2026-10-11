<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput\Actions\HidePasswordAction;
use Filament\Forms\Components\TextInput\Actions\ShowPasswordAction;
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
                    ->prefix('Prefix')
                    ->suffix('Suffix')
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
                    ->prefixAction(
                        Action::make('post')
                            ->icon(Heroicon::Star)
                            ->url('/')
                            ->postToUrl(),
                    )
                    ->suffixActions([
                        ShowPasswordAction::make()->extraAttributes(['data-testid' => 'show-password'], merge: true),
                        HidePasswordAction::make()->extraAttributes(['data-testid' => 'hide-password'], merge: true),
                    ])
                    ->extraAttributes(['data-testid' => 'password-input']),

                TextInput::make('disabledPassword')
                    ->label('Disabled password')
                    ->password()
                    ->revealable()
                    ->disabled()
                    ->default('disabled-secret')
                    ->suffixActions([
                        ShowPasswordAction::make()->extraAttributes(['data-testid' => 'show-disabled-password'], merge: true),
                        HidePasswordAction::make()->extraAttributes(['data-testid' => 'hide-disabled-password'], merge: true),
                    ])
                    ->extraAttributes(['data-testid' => 'disabled-password-input']),

                TextInput::make('readOnlyPassword')
                    ->label('Read-only password')
                    ->password()
                    ->revealable()
                    ->readOnly()
                    ->default('read-only-secret')
                    ->suffixActions([
                        ShowPasswordAction::make()->extraAttributes(['data-testid' => 'show-read-only-password'], merge: true),
                        HidePasswordAction::make()->extraAttributes(['data-testid' => 'hide-read-only-password'], merge: true),
                    ])
                    ->extraAttributes(['data-testid' => 'read-only-password-input']),

                TextInput::make('responsivePassword')
                    ->label('Responsive password')
                    ->password()
                    ->revealable()
                    ->default('responsive-secret')
                    ->suffixActions([
                        ShowPasswordAction::make()
                            ->button()
                            ->labeledFrom('md')
                            ->extraAttributes(['data-testid' => 'show-responsive-password'], merge: true),
                        HidePasswordAction::make()
                            ->button()
                            ->labeledFrom('md')
                            ->extraAttributes(['data-testid' => 'hide-responsive-password'], merge: true),
                    ])
                    ->extraAttributes(['data-testid' => 'responsive-password-input']),

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
