<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Tables\Components\StubTableContent;

class PostsTableWithStubLayout extends PostsTable
{
    public bool $contained = true;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->contained($this->contained)
            ->layout(fn (Schema $schema): Schema => $schema->components([
                StubTableContent::make(),
            ]));
    }
}
