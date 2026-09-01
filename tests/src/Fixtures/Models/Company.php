<?php

namespace Filament\Tests\Fixtures\Models;

use Filament\Tests\Database\Factories\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    protected static function newFactory()
    {
        return CompanyFactory::new();
    }
}
