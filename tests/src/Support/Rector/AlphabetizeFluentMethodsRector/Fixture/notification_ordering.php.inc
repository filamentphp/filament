<?php

use Filament\Notifications\Notification;

Notification::make()
    ->success()
    ->duration(5000)
    ->body('This is the body')
    ->title('Notification Title')
    ->icon('heroicon-o-check')
    ->send();

?>
-----
<?php

use Filament\Notifications\Notification;

Notification::make()
    ->title('Notification Title')
    ->body('This is the body')
    ->duration(5000)
    ->icon('heroicon-o-check')
    ->success()
    ->send();

?>
