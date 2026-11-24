<?php

namespace Filament\Tests\Fixtures\Resources\Departments;

use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class CustomUpdateAuthorizationDepartmentResource extends EditAuthorizationDepartmentResource
{
    public static function getUpdateAuthorizationResponse(Model $record): Response
    {
        return Response::allow();
    }
}
