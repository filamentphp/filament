<?php

namespace Filament\Tests\Fixtures\Resources\Tickets\Resources;

use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Resources\Tickets\TicketResource;

class TicketDepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return TicketResource::asParent(static::class)
            ->relationship('departments')
            ->inverseRelationship('tickets');
    }
}
