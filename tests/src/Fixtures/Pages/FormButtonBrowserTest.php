<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class FormButtonBrowserTest extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    public ?string $token = null;

    public int $submissions = 0;

    public string $email = '';

    public string $renderer = 'embedded';

    public string $disabledMode = 'disabled';

    public function mount(): void
    {
        $this->renderer = request()->query('renderer', 'embedded');
        $this->disabledMode = request()->query('disabledMode', 'disabled');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([
                TextInput::make('email'),
            ])
                ->id('send-form')
                ->livewireSubmitHandler('send')
                ->footer([
                    $this->renderer === 'blade' ? View::make('pages.form-button-browser-test') : Actions::make([
                        Action::make('send')
                            ->submit('send')
                            ->disabled(fn (): bool => ($this->disabledMode !== 'aria') && blank($this->token))
                            ->tooltip(fn (): ?string => ($this->disabledMode === 'tooltip') ? 'Submit the form' : null)
                            ->extraAttributes(fn (): array => [
                                'data-testid' => 'send',
                                'aria-disabled' => (($this->disabledMode === 'aria') && blank($this->token)) ? 'true' : null,
                            ]),
                    ]),
                ]),
            Actions::make([
                Action::make('setToken')
                    ->alpineClickHandler("\$wire.set('token', 'abc')")
                    ->extraAttributes(['data-testid' => 'set-token']),
                Action::make('resetToken')
                    ->alpineClickHandler("\$wire.set('token', null)")
                    ->extraAttributes(['data-testid' => 'reset-token']),
            ]),
            Text::make(fn (): string => 'Token: ' . ($this->token ?? 'empty')),
            Text::make(fn (): string => 'Submissions: ' . $this->submissions),
        ]);
    }

    public function send(): void
    {
        $this->submissions++;
    }
}
