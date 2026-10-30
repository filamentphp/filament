<?php

namespace Filament\Auth\MultiFactor\Email\Contracts;

interface HasEmailAuthentication
{
    public function hasEmailAuthentication(): bool;

    public function saveEmailAuthentication(bool $condition): void;
}
