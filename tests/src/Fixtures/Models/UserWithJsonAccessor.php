<?php

namespace Filament\Tests\Fixtures\Models;

class UserWithJsonAccessor extends User
{
    public function getJsonAttribute(mixed $value): mixed
    {
        return is_array($value) ? $value : json_decode($value ?? 'null', associative: true);
    }
}
