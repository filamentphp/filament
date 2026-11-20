<?php

namespace Filament\Tests\Fixtures\Resources\Tickets\Pages;

class ListTicketsWithRelationshipSaveOptInInViewAction extends ListTicketsWithRelationshipInViewAction
{
    protected bool $saveRelationshipsWhenDisabled = true;
}
