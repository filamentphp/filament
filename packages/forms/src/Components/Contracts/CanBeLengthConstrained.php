<?php

namespace Filament\Forms\Components\Contracts;

interface CanBeLengthConstrained
{
    public function getLength(): ?int;

    public function getMaxLength(): ?int;

    public function getMinLength(): ?int;
}
