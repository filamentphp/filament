<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\OneTimeCodeInput;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class OneTimeCodeInputSubmitOnCompletionBrowserTest extends Page
{
    protected string $view = 'pages.one-time-code-input-submit-on-completion-browser-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?int $navigationSort = 35;

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public ?string $submittedCode = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->default('Ada Lovelace')
                    ->required()
                    ->extraInputAttributes(['data-testid' => 'required-sibling']),
                OneTimeCodeInput::make('code')
                    ->label('Test OTP Code')
                    ->extraAttributes(['data-testid' => 'code-input'])
                    ->submitOnCompletion(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->submittedCode = $this->form->getState()['code'];
    }

    public function resetCode(): void
    {
        $this->data['code'] = null;
    }
}
