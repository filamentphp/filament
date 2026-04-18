<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings\SettingsCluster;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageGeneral extends Page
{
    protected static ?string $cluster = SettingsCluster::class;

    protected static ?string $navigationLabel = 'General';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'site_name' => 'Acme Inc',
            'site_description' => 'Building tools that help teams collaborate and ship faster.',
            'timezone' => 'America/New_York',
            'date_format' => 'M d, Y',
            'support_email' => 'support@acme.com',
            'maintenance_mode' => false,
            'registration' => true,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Site details')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Site name')
                            ->required(),
                        Textarea::make('site_description')
                            ->label('Site description')
                            ->rows(3),
                        TextInput::make('support_email')
                            ->label('Support email')
                            ->email(),
                    ]),
                Section::make('Localization')
                    ->schema([
                        Select::make('timezone')
                            ->label('Timezone')
                            ->options([
                                'America/New_York' => 'Eastern Time (US & Canada)',
                                'America/Chicago' => 'Central Time (US & Canada)',
                                'America/Denver' => 'Mountain Time (US & Canada)',
                                'America/Los_Angeles' => 'Pacific Time (US & Canada)',
                                'Europe/London' => 'London',
                                'Europe/Paris' => 'Paris',
                                'Asia/Tokyo' => 'Tokyo',
                            ])
                            ->searchable(),
                        Select::make('date_format')
                            ->label('Date format')
                            ->options([
                                'M d, Y' => 'Mar 20, 2026',
                                'Y-m-d' => '2026-03-20',
                                'd/m/Y' => '20/03/2026',
                                'F j, Y' => 'March 20, 2026',
                            ]),
                    ])
                    ->columns(2),
                Section::make('Access')
                    ->schema([
                        Toggle::make('registration')
                            ->label('Allow public registration'),
                        Toggle::make('maintenance_mode')
                            ->label('Maintenance mode'),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make($this->getFormActions())
                            ->alignment('start'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $this->form->getState();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save changes')
                ->submit('form'),
        ];
    }
}
