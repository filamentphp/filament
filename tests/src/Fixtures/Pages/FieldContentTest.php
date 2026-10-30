<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextInput\Actions\HidePasswordAction;
use Filament\Forms\Components\TextInput\Actions\ShowPasswordAction;
use Filament\Pages\Page;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class FieldContentTest extends Page
{
    protected string $view = 'pages.field-content-test';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public string $contentPosition = 'beforeContent';

    public string $wrapper = 'default';

    public function mount(): void
    {
        $this->contentPosition = request('contentPosition', 'beforeContent');
        $this->wrapper = request('wrapper', 'default');
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        $field = TextInput::make('password')
            ->password()
            ->revealable()
            ->live()
            ->suffixActions([
                ShowPasswordAction::make()->extraAttributes(['data-testid' => 'show-password'], merge: true),
                HidePasswordAction::make()->extraAttributes(['data-testid' => 'hide-password'], merge: true),
            ])
            ->extraInputAttributes(['data-testid' => 'password']);

        $component = ($this->wrapper === 'fused')
            ? FusedGroup::make([
                TextInput::make('username'),
                $field,
            ])->label('Credentials')->id('credentials')
            : $field;

        if ($this->wrapper === 'blade') {
            $component->fieldWrapperView('test-plugin-wrapper');
        }

        $component->{$this->contentPosition}(static fn (Get $get): ?Text => filled($get('password'))
            ? Text::make('You will use this password to sign in.')
                ->extraAttributes(['data-testid' => 'password-note'])
            : null);

        return $form
            ->components([$component])
            ->statePath('data');
    }
}
