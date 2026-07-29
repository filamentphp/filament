<?php

namespace Filament\Support\Facades;

use Closure;
use Filament\Support\Csp\CspManager;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\HtmlString;

/**
 * @method static ?string getNonce()
 * @method static HtmlString getNonceHtml()
 * @method static bool hasNonce()
 * @method static void useNonce(string | Closure | null $nonce = null)
 *
 * @see CspManager
 */
class FilamentCsp extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CspManager::class;
    }
}
