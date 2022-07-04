<?php

namespace Filament\Tests\Admin\Fixtures\Pages;

use Filament\Pages\Page;

class Settings extends Page
{
    protected static string $view = 'fixtures.pages.settings';

    public function notificationManager(bool $redirect = false)
    {
        if ($redirect) {
            $this->redirect('/');
        }

        $this->notify('success', 'Saved!');
    }
}
