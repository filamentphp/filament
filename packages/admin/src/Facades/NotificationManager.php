<?php

namespace Filament\Facades;

use Livewire\Component;
use Livewire\Response;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void notify(string $status, string $message)
 * @method static Response handleLivewireResponses(Component $component, Response $response)
 *
 * @see \Filament\NotificationManager
 */
class NotificationManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Filament\NotificationManager::class;
    }
}
