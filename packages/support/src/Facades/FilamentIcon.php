<?php

namespace Filament\Support\Facades;

use Closure;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Filament\Support\Icons\Icon;
use Filament\Support\Icons\IconManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(array $icons)
 * @method static Icon | null resolve(string $name)
 *
 * @see IconManager
 */
class FilamentIcon extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return IconManager::class;
    }
}
