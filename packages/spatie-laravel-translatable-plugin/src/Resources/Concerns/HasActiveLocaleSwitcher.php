<?php

namespace Filament\Resources\Concerns;

use Filament\SpatieLaravelTranslatableContentDriver;
use Filament\Support\Contracts\TranslatableContentDriver;

trait HasActiveLocaleSwitcher
{
    public ?string $activeLocale = null;

    public ?array $translatableLocales = null;

    public function getActiveFormsLocale(): ?string
    {
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
