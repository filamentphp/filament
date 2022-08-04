<?php

namespace Filament;

use Closure;

class SpatieLaravelTranslatablePluginManager
{
    protected ?Closure $getLocaleLabelUsing = null;

    public function getLocaleLabelUsing(?Closure $callback): void
    {
        $this->getLocaleLabelUsing = $callback;
    }

    public function getLocaleLabel(string $locale, ?string $displayLocale = null): ?string
    {
        $displayLocale ??= app()->getLocale();

        if ($callback = $this->getLocaleLabelUsing) {
            return $callback($locale, $displayLocale);
        }

        return locale_get_display_name($locale, $displayLocale) ?: null;
    }
}
