<?php

namespace Filament\Tests\App\Fixtures\Resources\UserResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Tests\App\Fixtures\Resources\UserResource;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
