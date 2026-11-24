<?php

namespace Filament\Tests\Fixtures\Resources\Departments;

use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class EditAuthorizationDepartmentResource extends DepartmentResource
{
    public static function getEditAuthorizationResponse(Model $record): Response
    {
        $response = parent::getEditAuthorizationResponse($record);

        return $response->denied() || ($record->name !== 'Read only')
            ? $response
            : Response::deny('Editing is disabled by the resource.');
    }

    public static function getPages(): array
    {
        return [];
    }
}
