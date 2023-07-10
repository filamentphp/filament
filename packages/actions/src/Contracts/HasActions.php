<?php

namespace Filament\Actions\Contracts;

use Filament\Actions\Action;
use Filament\Support\Contracts\TranslatableContentDriver;

interface HasActions
{
    /**
     * @param  string | array<string>  $name
     */
    public function getAction(string | array $name): ?Action;

    public function getActiveActionsLocale(): ?string;

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver;
}
