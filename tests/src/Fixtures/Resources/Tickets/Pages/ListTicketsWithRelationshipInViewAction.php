<?php

namespace Filament\Tests\Fixtures\Resources\Tickets\Pages;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class ListTicketsWithRelationshipInViewAction extends ListTickets
{
    protected bool $saveRelationshipsWhenDisabled = false;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('departments')
                ->relationship('departments', 'name')
                ->multiple()
                ->saveRelationshipsWhenDisabled($this->saveRelationshipsWhenDisabled),
        ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema;
    }
}
