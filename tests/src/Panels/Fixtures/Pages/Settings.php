<?php

namespace Filament\Tests\Panels\Fixtures\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schema\Schema;

class Settings extends Page
{
    protected static string $view = 'app.fixtures.pages.settings';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

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
            ->schema([
                TextInput::make('name')->required(),
            ]);
    }

    public function save()
    {
        $this->form->getState();
    }
}
