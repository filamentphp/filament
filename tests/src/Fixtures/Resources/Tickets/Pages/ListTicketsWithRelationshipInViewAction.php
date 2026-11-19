<?php

namespace Filament\Tests\Fixtures\Resources\Tickets\Pages;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class ListTicketsWithRelationshipInViewAction extends ListTickets
{
    protected bool $saveRelationshipsWhenDisabled = false;

    protected bool $isRelationshipSaved = false;

    public function form(Schema $schema): Schema
    {
        $relationship = Select::make('departments')
            ->relationship('departments', 'name')
            ->multiple()
            ->saveRelationshipsWhenDisabled($this->saveRelationshipsWhenDisabled);

        if ($this->isRelationshipSaved) {
            $relationship->saved();
        }

        return $schema->components([
            $relationship,
        ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema;
    }
}
