<?php

namespace Filament\Models\Contracts;

interface HasTimezone
{
    public function getFilamentTimezone(): ?string;
}
