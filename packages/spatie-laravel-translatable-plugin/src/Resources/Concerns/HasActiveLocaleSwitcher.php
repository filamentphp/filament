<?php

namespace Filament\Resources\Concerns;

use Filament\SpatieLaravelTranslatableContentDriver;
use Filament\Support\Contracts\TranslatableContentDriver;

trait HasActiveLocaleSwitcher
{
    public ?string $activeLocale = null;

    public function getActiveFormsLocale(): ?string
    {
        if (! in_array($this->activeLocale, $this->getTranslatableLocales())) {
            return null;
        }

        return $this->activeLocale;
    }

    public function getActiveActionsLocale(): ?string
    {
        return $this->activeLocale;
    }

    /**
     * @return class-string<TranslatableContentDriver> | null
     */
    public function getFilamentTranslatableContentDriver(): ?string
    {
        return SpatieLaravelTranslatableContentDriver::class;
    }
}
