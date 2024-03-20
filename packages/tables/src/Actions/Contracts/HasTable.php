<?php

namespace Filament\Tables\Actions\Contracts;

use Filament\Tables\Table;

interface HasTable
{
    public function table(Table $table): static;

    public function getTable(): Table;
}
