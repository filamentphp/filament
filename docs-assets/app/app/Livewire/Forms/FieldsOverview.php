<?php

namespace App\Livewire\Forms;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Livewire\Component;

class FieldsOverview extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->columns(1)
            ->components([
                Group::make()
                    ->id('account-settings')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Section::make('Account settings')
                            ->description('Manage your account preferences')
                            ->columns([
                                'sm' => 1,
                                'md' => 2,
                                'lg' => 2,
                            ])
                            ->collapsible()
                            ->schema([
                                TextInput::make('username')
                                    ->required(),

                                TextInput::make('password')
                                    ->password()
                                    ->revealable(),

                                Toggle::make('two_factor_auth')
                                    ->label('Enable two-factor authentication')
                                    ->helperText('Increase your account security by enabling 2FA')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->inline(),

                                ToggleButtons::make('theme_preference')
                                    ->label('Theme preference')
                                    ->options([
                                        'light' => 'Light',
                                        'dark' => 'Dark',
                                        'system' => 'System',
                                    ])
                                    ->inline()
                                    ->default('system')
                                    ->icons([
                                        'light' => Heroicon::OutlinedSun,
                                        'dark' => Heroicon::OutlinedMoon,
                                        'system' => Heroicon::OutlinedComputerDesktop,
                                    ]),

                                ColorPicker::make('accent_color')
                                    ->default('#3490dc'),

                                CheckboxList::make('notifications')
                                    ->label('Notification preferences')
                                    ->options([
                                        'email' => 'Email notifications',
                                        'push' => 'Push notifications',
                                        'sms' => 'SMS notifications',
                                    ])
                                    ->descriptions([
                                        'email' => 'Receive updates via email',
                                        'push' => 'Get instant notifications on your devices',
                                        'sms' => 'Get text messages for urgent updates',
                                    ])
                                    ->default(['email']),
                            ]),

                    ]),
            ]);
    }

    public function render()
    {
        return view('livewire.forms.overview');
    }
}
