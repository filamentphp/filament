<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\OneTimeCodeInput;
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

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                OneTimeCodeInput::make('code')
                    ->label('Test OTP Code')
                    ->submitOnCompletion(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function resetCode(): void
    {
        $this->data['code'] = null;
    }
}
