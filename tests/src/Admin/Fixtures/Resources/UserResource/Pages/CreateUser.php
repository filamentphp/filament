<?php

namespace Filament\Tests\Admin\Fixtures\Resources\UserResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Tests\Admin\Fixtures\Resources\UserResource;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
