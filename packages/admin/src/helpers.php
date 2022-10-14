<?php

namespace Filament;

use Illuminate\Translation\MessageSelector;

if (! function_exists('Filament\locale_has_pluralization')) {
    function locale_has_pluralization(): bool
    {
        return (new MessageSelector())->getPluralIndex(app()->getLocale(), 10) > 0;
    }
}
