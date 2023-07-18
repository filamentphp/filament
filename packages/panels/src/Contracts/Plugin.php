<?php

namespace Filament\Contracts;

use Filament\Panel;

interface Plugin
{
    public function getId(): string;

    public function register(Panel $panel): void;

    public function boot(Panel $panel): void;
}
