<?php

namespace Filament\Support\Facades;

use Filament\Support\ModelSubscription;
use Filament\Support\RealtimeManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ModelSubscription subscribe(class-string<Model>|ModelSubscription|null $model = null)
 * @method static RealtimeManager unsubscribe(class-string<Model>|ModelSubscription $subscription)
 * @method static array<string, ModelSubscription> getSubscriptions()
 *
 * @see RealtimeManager
 */
class FilamentRealtime extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return RealtimeManager::class;
    }
}
