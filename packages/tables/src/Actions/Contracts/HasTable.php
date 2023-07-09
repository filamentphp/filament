<?php

namespace Filament\Tables\Actions\Contracts;

use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables\Table;

interface HasTable
{
    public function table(Table $table): static;
}
