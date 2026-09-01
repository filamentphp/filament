<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class Settings extends Page
{
    protected string $view = 'pages.settings';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 2;

    public $name;

    public function notificationManager(bool $redirect = false)
    {
        if ($redirect) {
            $this->redirect('/');
        }

        Notification::make()
            ->title('Saved!')
            ->success()
            ->send();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                TextInput::make('name')->required(),
            ]);
    }

    public function save()
    {
        $this->form->getState();
    }
}
