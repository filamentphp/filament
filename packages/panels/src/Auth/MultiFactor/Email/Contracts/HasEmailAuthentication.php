<?php

namespace Filament\Auth\MultiFactor\Email\Contracts;

interface HasEmailAuthentication
{
    public function hasEmailAuthentication(): bool;

    public function toggleEmailAuthentication(bool $condition): void;
}
