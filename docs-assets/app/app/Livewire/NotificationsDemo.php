<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Component;

class NotificationsDemo extends Component
{
    public function mount(): void
    {
        DatabaseNotification::truncate();

        if (! auth()->check()) {
            auth()->login(User::first());
        }

        // Send the notifications during mount, so the `database-notifications`
        // modal already contains them when it first renders. Sending them from
        // `openDatabaseNotifications()` instead would make the modal first
        // render empty and rely on a Livewire refresh to morph them in, which
        // races the screenshot capture.
        if (request()->query('method') === 'openDatabaseNotifications') {
            $this->sendDatabaseNotifications();
        }
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
            ->icon(Heroicon::OutlinedDocumentText)
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

    public function positioning(): void
    {
        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->body('Changes to the post have been saved.')
            ->send();
    }

    public function openDatabaseNotifications()
    {
        $this->dispatch(
            'open-modal',
            id: 'database-notifications',
        );
    }

    protected function sendDatabaseNotifications(): void
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

        // Backdate the notifications so their relative timestamps always
        // render as `2 minutes ago` in screenshots, instead of flip-flopping
        // between `0 seconds ago` and `1 second ago` depending on timing.
        DatabaseNotification::query()->update(['created_at' => now()->subMinutes(2)]);
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
