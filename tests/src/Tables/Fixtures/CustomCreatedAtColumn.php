<?php

namespace Filament\Tests\Tables\Fixtures;

use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

final class CustomCreatedAtColumn extends TextColumn
{
    public static function getDefaultName(): ?string
    {
        return Model::CREATED_AT;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->searchable(isIndividual: true, isGlobal: false)
            ->sortable()
            ->toggleable();
    }
}
