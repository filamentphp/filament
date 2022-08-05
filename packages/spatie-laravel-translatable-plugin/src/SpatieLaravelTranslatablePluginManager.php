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

        $label = null;

        if ($callback = $this->getLocaleLabelUsing) {
            $label = $callback($locale, $displayLocale);
        }

        return $label ?? (locale_get_display_name($locale, $displayLocale) ?: null);
    }
}
