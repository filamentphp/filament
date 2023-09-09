<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Component;

class NotificationsDemo extends Component
{
    public function mount(): void
    {
        User::truncate();
        DatabaseNotification::truncate();

        $user = User::factory()->create();
        auth()->login($user);
    }

    public function success(): void
    {
        Notification::make()
            ->title('Saved')
            ->success()
            ->send();
    }

    public function icon(): void
    {
        Notification::make()
            ->title('Saved successfully')
            ->icon('heroicon-o-document-text')
            ->iconColor('success')
            ->send();
    }

    public function statuses(): void
    {
        Notification::make()
            ->title('Here\'s some information')
            ->info()
            ->send();

        Notification::make()
            ->title('Something went wrong')
            ->danger()
            ->send();

        Notification::make()
            ->title('You\'re not allowed to edit')
            ->warning()
            ->send();

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
    }

    public function color(): void
    {
        Notification::make()
            ->title('Saved successfully')
            ->color('success')
            ->send();
    }

    public function body(): void
    {
        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->body('Changes to the post have been saved.')
            ->send();
    }

    public function actions(): void
    {
        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->body('Changes to the post have been saved.')
            ->actions([
                Action::make('view')
                    ->button(),
                Action::make('undo')
                    ->color('gray'),
            ])
            ->send();
    }

    public function openDatabaseNotifications()
    {
        $user = auth()->user();

        Notification::make()
            ->title('Saved successfully')
            ->body('Keep going! You\'re doing great')
            ->success()
            ->sendToDatabase($user);

        Notification::make()
            ->title('You\'re not allowed to edit')
            ->body('You weren\'t supposed to do that, naughty...')
            ->warning()
            ->sendToDatabase($user);

        Notification::make()
            ->title('Something went wrong')
            ->body('Uh oh! Let\'s try that again')
            ->danger()
            ->sendToDatabase($user);

        Notification::make()
            ->title('Here\'s some information')
            ->body('Filament is here to help you :)')
            ->info()
            ->sendToDatabase($user);

        $this->dispatch(
            'open-modal',
            id: 'database-notifications',
        );

        $this->dispatch('databaseNotificationsSent');
    }

    public function call(string $method): void
    {
        $this->{$method}();
    }

    public function render()
    {
        return view('livewire.notifications');
    }
}
