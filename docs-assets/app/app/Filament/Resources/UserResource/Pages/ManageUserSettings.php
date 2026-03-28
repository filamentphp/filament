<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * @property-read Schema $form
 */
class ManageUserSettings extends Page
{
    protected static string $resource = UserResource::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Settings';

    protected string $view = 'filament.resources.user-resource.pages.manage-user-settings';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'timezone' => 'America/New_York',
            'locale' => 'en',
            'email_notifications' => true,
            'marketing_emails' => false,
            'two_factor' => true,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make('Preferences')
                        ->schema([
                            Select::make('timezone')
                                ->options([
                                    'America/New_York' => 'Eastern Time (US & Canada)',
                                    'America/Chicago' => 'Central Time (US & Canada)',
                                    'America/Denver' => 'Mountain Time (US & Canada)',
                                    'America/Los_Angeles' => 'Pacific Time (US & Canada)',
                                    'Europe/London' => 'London',
                                    'Europe/Paris' => 'Paris',
                                ])
                                ->required(),
                            Select::make('locale')
                                ->label('Language')
                                ->options([
                                    'en' => 'English',
                                    'es' => 'Spanish',
                                    'fr' => 'French',
                                    'de' => 'German',
                                ])
                                ->required(),
                        ])
                        ->columns(2),
                    Section::make('Notifications')
                        ->schema([
                            Toggle::make('email_notifications')
                                ->label('Email notifications')
                                ->helperText('Receive email notifications for important updates.'),
                            Toggle::make('marketing_emails')
                                ->label('Marketing emails')
                                ->helperText('Receive promotional offers and newsletters.'),
                        ]),
                    Section::make('Security')
                        ->schema([
                            Toggle::make('two_factor')
                                ->label('Two-factor authentication')
                                ->helperText('Add an extra layer of security to your account.'),
                        ]),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->submit('save'),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();

        Notification::make()
            ->success()
            ->title('Settings saved')
            ->send();
    }
}
