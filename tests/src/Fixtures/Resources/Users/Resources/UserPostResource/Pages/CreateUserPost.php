<?php

namespace Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Tests\Fixtures\Resources\Users\Resources\UserPostResource;

class CreateUserPost extends CreateRecord
{
    protected static string $resource = UserPostResource::class;
}
