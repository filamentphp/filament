<?php

namespace Filament\Pages\Contracts;

interface HasModel
{
    public function getModel(): string;

    public function getModelLabel(): ?string;

    public function getPluralModelLabel(): ?string;
}
