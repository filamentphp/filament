<?php

namespace Filament\Tests\Actions;

use Filament\PluginServiceProvider;
use Filament\Tests\Actions\Fixtures\Pages\Actions;

class ActionsServiceProvider extends PluginServiceProvider
{
    public static string $name = 'actions';

    protected function getPages(): array
    {
        return [
            Actions::class,
        ];
    }
}
