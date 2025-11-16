<?php

use Filament\Actions\Action;

Action::make('test')
    ->color('danger')
    ->requiresConfirmation()
    ->modalHeading('Delete')
    ->after(fn () => null)
    ->successNotification('Success!')
    ->action(fn () => null)
    ->before(fn () => null)
    ->failureNotification('Failed!');

?>
-----
<?php

use Filament\Actions\Action;

Action::make('test')
    ->color('danger')
    ->modalHeading('Delete')
    ->requiresConfirmation()
    ->before(fn () => null)
    ->action(fn () => null)
    ->after(fn () => null)
    ->successNotification('Success!')
    ->failureNotification('Failed!');

?>
