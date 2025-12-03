<?php

namespace Filament\Tests\Fixtures\Models;

use Filament\Tests\Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return CompanyFactory::new();
    }
}
